<?php

namespace WpStarter\Queue\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_PROPERTY)]
class WithoutRelations
{
    //
}
