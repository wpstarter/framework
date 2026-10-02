<?php

namespace WpStarter\Tests\Integration\Auth\Fixtures\Models\Nested;

use WpStarter\Foundation\Auth\User as Authenticatable;

class SubTestUser extends Authenticatable
{
    public $table = 'users';
    public $timestamps = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var string[]
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
