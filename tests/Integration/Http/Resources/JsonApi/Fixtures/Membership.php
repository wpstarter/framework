<?php

namespace WpStarter\Tests\Integration\Http\Resources\JsonApi\Fixtures;

use WpStarter\Database\Eloquent\Relations\Pivot;

class Membership extends Pivot
{
    protected $table = 'team_user';
}
