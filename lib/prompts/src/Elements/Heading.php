<?php

namespace WpStarter\Prompts\Elements;

class Heading implements ElementContract
{
    public function __construct(public readonly string $text)
    {
        //
    }
}
