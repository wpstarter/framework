<?php

namespace WpStarter\Tests\Database\Fixtures\Models;

use WpStarter\Database\Eloquent\Attributes\UseResource;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Tests\Database\Fixtures\Resources\EloquentResourceTestJsonResource;

#[UseResource(EloquentResourceTestJsonResource::class)]
class EloquentResourceTestResourceModelWithUseResourceAttribute extends Model
{
    //
}
