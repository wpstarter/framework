<?php

use WpStarter\Database\Eloquent\Factories\Factory;
use WpStarter\Database\Eloquent\Factories\HasFactory;
use WpStarter\Database\Eloquent\MassPrunable;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Eloquent\SoftDeletes;
use WpStarter\Foundation\Auth\User as Authenticatable;
use WpStarter\Notifications\HasDatabaseNotifications;

class User extends Authenticatable
{
    use HasDatabaseNotifications;
    /** @use HasFactory<UserFactory> */
    use HasFactory;
    use MassPrunable;
    use SoftDeletes;

    protected static string $factory = UserFactory::class;
}

/** @extends Factory<User> */
class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [];
    }
}

class Post extends Model
{
}

enum UserType
{
}
