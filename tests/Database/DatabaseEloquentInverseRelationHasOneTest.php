<?php

namespace WpStarter\Tests\Database;

use WpStarter\Database\Capsule\Manager as DB;
use WpStarter\Database\Eloquent\Factories\Factory;
use WpStarter\Database\Eloquent\Factories\HasFactory;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Database\Eloquent\Model as Eloquent;
use WpStarter\Database\Eloquent\Relations\BelongsTo;
use WpStarter\Database\Eloquent\Relations\HasOne;
use PHPUnit\Framework\TestCase;

class DatabaseEloquentInverseRelationHasOneTest extends TestCase
{
    /**
     * Setup the database schema.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $db = new DB;

        $db->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $db->bootEloquent();
        $db->setAsGlobal();

        $this->createSchema();
    }

    protected function createSchema()
    {
        $this->schema()->create('test_parent', function ($table) {
            $table->increments('id');
            $table->timestamps();
        });

        $this->schema()->create('test_child', function ($table) {
            $table->increments('id');
            $table->foreignId('parent_id')->unique();
            $table->timestamps();
        });
    }

    /**
     * Tear down the database schema.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        $this->schema()->drop('test_parent');
        $this->schema()->drop('test_child');

        parent::tearDown();
    }

    public function testHasOneInverseRelationIsProperlySetToParentWhenLazyLoaded()
    {
        HasOneInverseChildModel::factory(5)->create();
        $models = HasOneInverseParentModel::all();

        foreach ($models as $parent) {
            $this->assertFalse($parent->relationLoaded('child'));
            $child = $parent->child;
            $this->assertTrue($child->relationLoaded('parent'));
            $this->assertSame($parent, $child->parent);
        }
    }

    public function testHasOneInverseRelationIsProperlySetToParentWhenEagerLoaded()
    {
        HasOneInverseChildModel::factory(5)->create();

        $models = HasOneInverseParentModel::with('child')->get();

        foreach ($models as $parent) {
            $child = $parent->child;

            $this->assertTrue($child->relationLoaded('parent'));
            $this->assertSame($parent, $child->parent);
        }
    }

    public function testHasOneInverseRelationIsProperlySetToParentWhenMaking()
    {
        $parent = HasOneInverseParentModel::create();

        $child = $parent->child()->make();

        $this->assertTrue($child->relationLoaded('parent'));
        $this->assertSame($parent, $child->parent);
    }

    public function testHasOneInverseRelationIsProperlySetToParentWhenCreating()
    {
        $parent = HasOneInverseParentModel::create();

        $child = $parent->child()->create();

        $this->assertTrue($child->relationLoaded('parent'));
        $this->assertSame($parent, $child->parent);
    }

    public function testHasOneInverseRelationIsProperlySetToParentWhenCreatingQuietly()
    {
        $parent = HasOneInverseParentModel::create();

        $child = $parent->child()->createQuietly();

        $this->assertTrue($child->relationLoaded('parent'));
        $this->assertSame($parent, $child->parent);
    }

    public function testHasOneInverseRelationIsProperlySetToParentWhenForceCreating()
    {
        $parent = HasOneInverseParentModel::create();

        $child = $parent->child()->forceCreate();

        $this->assertTrue($child->relationLoaded('parent'));
        $this->assertSame($parent, $child->parent);
    }

    public function testHasOneInverseRelationIsProperlySetToParentWhenSaving()
    {
        $parent = HasOneInverseParentModel::create();
        $child = HasOneInverseChildModel::make();

        $this->assertFalse($child->relationLoaded('parent'));
        $parent->child()->save($child);

        $this->assertTrue($child->relationLoaded('parent'));
        $this->assertSame($parent, $child->parent);
    }

    public function testHasOneInverseRelationIsProperlySetToParentWhenSavingQuietly()
    {
        $parent = HasOneInverseParentModel::create();
        $child = HasOneInverseChildModel::make();

        $this->assertFalse($child->relationLoaded('parent'));
        $parent->child()->saveQuietly($child);

        $this->assertTrue($child->relationLoaded('parent'));
        $this->assertSame($parent, $child->parent);
    }

    public function testHasOneInverseRelationIsProperlySetToParentWhenUpdating()
    {
        $parent = HasOneInverseParentModel::create();
        $child = HasOneInverseChildModel::factory()->create();

        $this->assertTrue($parent->isNot($child->parent));

        $parent->child()->save($child);

        $this->assertTrue($parent->is($child->parent));
        $this->assertSame($parent, $child->parent);
    }

    /**
     * Helpers...
     */

    /**
     * Get a database connection instance.
     *
     * @return \WpStarter\Database\Connection
     */
    protected function connection($connection = 'default')
    {
        return Eloquent::getConnectionResolver()->connection($connection);
    }

    /**
     * Get a schema builder instance.
     *
     * @return \WpStarter\Database\Schema\Builder
     */
    protected function schema($connection = 'default')
    {
        return $this->connection($connection)->getSchemaBuilder();
    }
}

class HasOneInverseParentModel extends Model
{
    use HasFactory;

    protected $table = 'test_parent';

    protected $fillable = ['id'];

    protected static function newFactory()
    {
        return new HasOneInverseParentModelFactory();
    }

    public function child(): HasOne
    {
        return $this->hasOne(HasOneInverseChildModel::class, 'parent_id')->inverse('parent');
    }
}

class HasOneInverseParentModelFactory extends Factory
{
    protected $model = HasOneInverseParentModel::class;

    public function definition()
    {
        return [];
    }
}

class HasOneInverseChildModel extends Model
{
    use HasFactory;

    protected $table = 'test_child';
    protected $fillable = ['id', 'parent_id'];

    protected static function newFactory()
    {
        return new HasOneInverseChildModelFactory();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(HasOneInverseParentModel::class, 'parent_id');
    }
}

class HasOneInverseChildModelFactory extends Factory
{
    protected $model = HasOneInverseChildModel::class;

    public function definition()
    {
        return [
            'parent_id' => HasOneInverseParentModel::factory(),
        ];
    }
}
