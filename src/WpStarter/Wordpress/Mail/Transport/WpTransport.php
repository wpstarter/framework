<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 28-May-19
 * Time: 2:23 PM
 */

namespace WpStarter\Wordpress\Mail\Transport;

use PHPMailer\PHPMailer\PHPMailer;
use WpStarter\Mail\Transport\Transport;

use Swift_Mime_SimpleMessage;

class WpTransport extends Transport
{
    /**
     * @var PHPMailer
     */
    protected $phpMailer;
    /**
     * @var Swift_Mime_SimpleMessage
     */
    protected $message;
    public function __construct($config)
    {
        add_action('phpmailer_init',[$this,'setPhpMailer']);
    }

    /**
     * @param PHPMailer $phpMailer
     * @return void
     */
    public function setPhpMailer($phpMailer){
        if($children=$this->message->getChildren()){
            foreach ($children as $child){
                if($child instanceof \Swift_Mime_EmbeddedFile) {
                    $body=$child->getBody();
                    $phpMailer->addStringEmbeddedImage(
                        $body,
                        $child->getId(),
                        $child->getFilename(),$phpMailer::ENCODING_BASE64,$child->getContentType());
                }elseif($child instanceof \Swift_Mime_Attachment){
                    $phpMailer->addStringAttachment(
                        $body,
                        $child->getFileName(),
                        $phpMailer::ENCODING_BASE64,
                        $child->getContentType(),
                    );
                }
            }
        }
    }

    function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
        $this->message=$message;
        $tos=$message->getTo();
        $subject=$message->getSubject();
        $body=$message->getBody();
        $headers=$message->getHeaders()->get('Content-Type')->toString();
        $failedRecipients = (array) $failedRecipients;
        $sent=0;

        foreach (array_keys($tos) as $to) {
            if(!wp_mail($to, $subject,$body,$headers)){
                $failedRecipients[]=$to;
            }else{
                $sent++;
            }
        }
        return $sent;
    }
}