<?php

namespace WpStarter\Tests\Integration\Database\Fixtures;

use WpStarter\Database\Eloquent\Model;

class PostStringyKey extends Model
{
    public $table = 'my_posts';

    public $primaryKey = 'my_id';
}
