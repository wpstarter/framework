<?php
/**
 * Created by PhpStorm.
 * User: alt
 * Date: 28-May-19
 * Time: 2:23 PM
 */

namespace WpStarter\Wordpress\Mail\Transport;

use WpStarter\Mail\Transport\Transport;

use Swift_Mime_SimpleMessage;

class WpTransport extends Transport
{
    public function __construct($config)
    {
    }

    function send(Swift_Mime_SimpleMessage $message, &$failedRecipients = null)
    {
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