<?php

namespace WpStarter\Tests\Database\Fixtures\Models\Money;

use WpStarter\Database\Eloquent\Factories\HasFactory;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Tests\Database\Fixtures\Factories\Money\PriceFactory;

class Price extends Model
{
    use HasFactory;

    protected $table = 'prices';

    public static function factory()
    {
        return PriceFactory::new();
    }
}
