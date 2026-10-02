<?php

namespace WpStarter\Contracts\Mail;

interface Attachable
{
    /**
     * Get an attachment instance for this entity.
     *
     * @return \WpStarter\Mail\Attachment
     */
    public function toMailAttachment();
}
