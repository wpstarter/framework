<?php

namespace WpStarter\Contracts\Mail;

interface Factory
{
    /**
     * Get a mailer instance by name.
     *
     * @param  string|null  $name
     * @return \WpStarter\Contracts\Mail\Mailer
     */
    public function mailer($name = null);
}
