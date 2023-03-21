<?php

namespace WpStarter\Tests\Integration\Http\Fixtures;

use WpStarter\Database\Eloquent\Model;

class Author extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];
}
