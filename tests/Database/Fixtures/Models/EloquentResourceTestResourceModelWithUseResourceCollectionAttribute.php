<?php

namespace WpStarter\Tests\Database\Fixtures\Models;

use WpStarter\Database\Eloquent\Attributes\UseResourceCollection;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Tests\Database\Fixtures\Resources\EloquentResourceTestJsonResourceCollection;

#[UseResourceCollection(EloquentResourceTestJsonResourceCollection::class)]
class EloquentResourceTestResourceModelWithUseResourceCollectionAttribute extends Model
{
    //
}
