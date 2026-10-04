<?php

namespace WpStarter\Wordpress\Mail\Transport;

use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\AbstractMultipartPart;
use Symfony\Component\Mime\Part\AbstractPart;
use Symfony\Component\Mime\Part\DataPart;
use Symfony\Component\Mime\Part\TextPart;

class WpTransport extends AbstractTransport
{
    protected function doSend(SentMessage $message): void
    {
        $email = $message->getOriginalMessage();

        if (! $email instanceof Email) {
            throw new TransportException('The WordPress mail transport requires a Symfony Email message.');
        }

        $text = $html = null;
        $textCharset = $htmlCharset = 'UTF-8';
        /**
         * @var DataPart[] $attachments
         */
        $attachments = [];

        // Use the generated body to preserve Symfony's inline Content-ID replacements.
        foreach ($this->getParts($email->getBody()) as $part) {
            if ($part instanceof DataPart) {
                $attachments[] = $part;
            } elseif ($part instanceof TextPart && $part->getMediaType() === 'text') {
                if ($part->getMediaSubtype() === 'html') {
                    $html = $part->getBody();
                    $htmlCharset = $part->getPreparedHeaders()->getHeaderParameter('Content-Type', 'charset') ?? 'UTF-8';
                } elseif ($part->getMediaSubtype() === 'plain') {
                    $text = $part->getBody();
                    $textCharset = $part->getPreparedHeaders()->getHeaderParameter('Content-Type', 'charset') ?? 'UTF-8';
                }
            }
        }

        $headers = [];
        foreach ($email->getPreparedHeaders()->all() as $header) {
            if (in_array(strtolower($header->getName()), ['to', 'from', 'cc', 'bcc', 'reply-to', 'subject', 'content-type', 'content-transfer-encoding', 'mime-version', 'message-id'])) {
                continue;
            }

            $headers[] = preg_replace('/\r\n[ \t]+/', ' ', $header->toString());
        }
        foreach (['getFrom' => 'From', 'getCc' => 'Cc', 'getBcc' => 'Bcc', 'getReplyTo' => 'Reply-To'] as $getter => $name) {
            foreach ($email->$getter() as $address) {
                $headers[] = $name.': '.$address->toString();
            }
        }
        $headers[] = 'Content-Type: '.($html !== null ? 'text/html; charset='.$htmlCharset : 'text/plain; charset='.$textCharset);

        $initialize = function ($phpMailer) use ($message, $email, $text, $html, $attachments) {
            $phpMailer->AltBody = $html !== null ? ($text ?? '') : '';
            $phpMailer->MessageID = '<'.$message->getMessageId().'>';
            $phpMailer->Sender = $message->getEnvelope()->getSender()->getAddress();

            // Honor the envelope without exposing blind recipients in the To header.
            $phpMailer->clearAllRecipients();
            $recipients = [];
            foreach ($message->getEnvelope()->getRecipients() as $address) {
                $recipients[$address->getAddress()] = $address;
            }
            foreach (['getTo' => 'addAddress', 'getCc' => 'addCC', 'getBcc' => 'addBCC'] as $getter => $adder) {
                foreach ($email->$getter() as $address) {
                    if (isset($recipients[$address->getAddress()])) {
                        $phpMailer->$adder($address->getAddress(), $address->getName());
                        unset($recipients[$address->getAddress()]);
                    }
                }
            }
            foreach ($recipients as $address) {
                $phpMailer->addBCC($address->getAddress(), $address->getName());
            }

            foreach ($attachments as $attachment) {
                if ($attachment->getDisposition() === 'inline') {
                    $phpMailer->addStringEmbeddedImage($attachment->getBody(), $attachment->getContentId(), $attachment->getFilename() ?? '', 'base64', $attachment->getContentType());
                } else {
                    $phpMailer->addStringAttachment($attachment->getBody(), $attachment->getFilename() ?? '', 'base64', $attachment->getContentType());
                }
            }
        };

        add_action('phpmailer_init', $initialize);
        try {
            if (! wp_mail($this->stringifyAddresses($email->getTo()), $email->getSubject() ?? '', $html ?? $text ?? '', $headers)) {
                throw new TransportException('WordPress could not send the email via wp_mail.');
            }
        } finally {
            remove_action('phpmailer_init', $initialize);
        }
    }

    /**
     * @return iterable<AbstractPart>
     */
    protected function getParts(AbstractPart $part): iterable
    {
        if ($part instanceof AbstractMultipartPart) {
            foreach ($part->getParts() as $child) {
                yield from $this->getParts($child);
            }
        } else {
            yield $part;
        }
    }

    public function __toString(): string
    {
        return 'wp';
    }
}
