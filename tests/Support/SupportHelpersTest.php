<?php

namespace WpStarter\Tests\Support;

use ArrayAccess;
use ArrayIterator;
use Carbon\CarbonInterval;
use Countable;
use Error;
use WpStarter\Contracts\Support\Htmlable;
use WpStarter\Database\Eloquent\Model;
use WpStarter\Filesystem\Filesystem;
use WpStarter\Support\Env;
use WpStarter\Support\Optional;
use WpStarter\Support\Sleep;
use WpStarter\Support\Stringable;
use WpStarter\Tests\Support\Fixtures\IntBackedEnum;
use WpStarter\Tests\Support\Fixtures\StringBackedEnum;
use IteratorAggregate;
use LogicException;
use Mockery as m;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use RuntimeException;
use stdClass;
use Traversable;

class SupportHelpersTest extends TestCase
{
    protected function setUp(): void
    {
        mkdir(__DIR__.'/tmp');

        parent::setUp();
    }

    protected function tearDown(): void
    {
        if (is_dir(__DIR__.'/tmp')) {
            (new Filesystem)->deleteDirectory(__DIR__.'/tmp');
        }

        parent::tearDown();
    }

    public function testE()
    {
        $str = 'A \'quote\' is <b>bold</b>';
        $this->assertSame('A &#039;quote&#039; is &lt;b&gt;bold&lt;/b&gt;', ws_e($str));

        $html = m::mock(Htmlable::class);
        $html->shouldReceive('toHtml')->andReturn($str);
        $this->assertEquals($str, ws_e($html));
    }

    public function testEWithInvalidCodePoints()
    {
        $str = mb_convert_encoding('føø bar', 'ISO-8859-1', 'UTF-8');
        $this->assertEquals('f�� bar', ws_e($str));
    }

    public function testEWithEnums()
    {
        $enumValue = StringBackedEnum::ADMIN_LABEL;
        $this->assertSame('I am &#039;admin&#039;', ws_e($enumValue));

        $enumValue = IntBackedEnum::ROLE_ADMIN;
        $this->assertSame('1', ws_e($enumValue));
    }

    public function testBlank()
    {
        $this->assertTrue(ws_blank(null));
        $this->assertTrue(ws_blank(''));
        $this->assertTrue(ws_blank('  '));
        $this->assertTrue(ws_blank(new Stringable('')));
        $this->assertTrue(ws_blank(new Stringable('  ')));
        $this->assertFalse(ws_blank(10));
        $this->assertFalse(ws_blank(true));
        $this->assertFalse(ws_blank(false));
        $this->assertFalse(ws_blank(0));
        $this->assertFalse(ws_blank(0.0));
        $this->assertFalse(ws_blank(new Stringable(' FooBar ')));

        $object = new SupportTestCountable();
        $this->assertTrue(ws_blank($object));
    }

    public function testBlankDoesntJsonSerializeModels()
    {
        $model = new class extends Model
        {
            public function jsonSerialize(): mixed
            {
                throw new RuntimeException('Model should not be serialized');
            }
        };

        $this->assertFalse(ws_blank($model));
    }

    public function testClassBasename()
    {
        $this->assertSame('Baz', ws_class_basename('Foo\Bar\Baz'));
        $this->assertSame('Baz', ws_class_basename('Baz'));
        // back-slash
        $this->assertSame('Baz', ws_class_basename('\Baz'));
        $this->assertSame('Baz', ws_class_basename('\\\\Baz\\'));
        $this->assertSame('Baz', ws_class_basename('\Foo\Bar\Baz\\'));
        $this->assertSame('Baz', ws_class_basename('\Foo/Bar\Baz/'));
        // forward-slash
        $this->assertSame('Baz', ws_class_basename('/Foo/Bar/Baz/'));
        $this->assertSame('Baz', ws_class_basename('/Foo///Bar/Baz//'));
        // accepts objects
        $this->assertSame('stdClass', ws_class_basename(new stdClass()));
        // edge-cases
        $this->assertSame('1', ws_class_basename(1));
        $this->assertSame('1', ws_class_basename('1'));
        $this->assertSame('', ws_class_basename(''));
        $this->assertSame('', ws_class_basename('\\'));
        $this->assertSame('', ws_class_basename('\\\\'));
        $this->assertSame('', ws_class_basename('/'));
        $this->assertSame('', ws_class_basename('///'));
        $this->assertSame('..', ws_class_basename('\Foo\Bar\Baz\\..\\'));
    }

    public function testWhen()
    {
        $this->assertEquals('Hello', ws_when(true, 'Hello'));
        $this->assertNull(ws_when(false, 'Hello'));
        $this->assertEquals('There', ws_when(1 === 1, 'There')); // strict types
        $this->assertEquals('There', ws_when(1 == '1', 'There')); // loose types
        $this->assertNull(ws_when(1 == 2, 'There'));
        $this->assertNull(ws_when('1', fn () => null));
        $this->assertNull(ws_when(0, fn () => null));
        $this->assertEquals('True', ws_when([1, 2, 3, 4], 'True')); // Array
        $this->assertNull(ws_when([], 'True')); // Empty Array = Falsy
        $this->assertEquals('True', ws_when(new StdClass, fn () => 'True')); // Object
        $this->assertEquals('World', ws_when(false, 'Hello', 'World'));
        $this->assertEquals('World', ws_when(1 === 0, 'Hello', 'World')); // strict types
        $this->assertEquals('World', ws_when(1 == '0', 'Hello', 'World')); // loose types
        $this->assertNull(ws_when('', fn () => 'There', fn () => null));
        $this->assertNull(ws_when(0, fn () => 'There', fn () => null));
        $this->assertEquals('False', ws_when([], 'True', 'False'));  // Empty Array = Falsy
        $this->assertTrue(ws_when(true, fn ($value) => $value, fn ($value) => ! $value)); // lazy evaluation
        $this->assertTrue(ws_when(false, fn ($value) => $value, fn ($value) => ! $value)); // lazy evaluation
        $this->assertEquals('Hello', ws_when(fn () => true, 'Hello')); // lazy evaluation condition
        $this->assertEquals('World', ws_when(fn () => false, 'Hello', 'World')); // lazy evaluation condition
    }

    public function testFilled()
    {
        $this->assertFalse(ws_filled(null));
        $this->assertFalse(ws_filled(''));
        $this->assertFalse(ws_filled('  '));
        $this->assertFalse(ws_filled(new Stringable('')));
        $this->assertFalse(ws_filled(new Stringable('  ')));
        $this->assertTrue(ws_filled(10));
        $this->assertTrue(ws_filled(true));
        $this->assertTrue(ws_filled(false));
        $this->assertTrue(ws_filled(0));
        $this->assertTrue(ws_filled(0.0));
        $this->assertTrue(ws_filled(new Stringable(' FooBar ')));

        $object = new SupportTestCountable();
        $this->assertFalse(ws_filled($object));
    }

    public function testValue()
    {
        $callable = new class
        {
            public function __call($method, $arguments)
            {
                return $arguments;
            }
        };

        $this->assertSame($callable, ws_value($callable, 'foo'));
        $this->assertSame('foo', ws_value('foo'));
        $this->assertSame('foo', ws_value(function () {
            return 'foo';
        }));
        $this->assertSame('foo', ws_value(function ($arg) {
            return $arg;
        }, 'foo'));
    }

    public function testObjectGet()
    {
        $class = new stdClass;
        $class->name = new stdClass;
        $class->name->first = 'Taylor';

        $this->assertSame('Taylor', ws_object_get($class, 'name.first'));
        $this->assertSame('Taylor', ws_object_get($class, 'name.first', 'default'));
    }

    public function testObjectGetDefaultValue()
    {
        $class = new stdClass;
        $class->name = new stdClass;
        $class->name->first = 'Taylor';

        $this->assertSame('default', ws_object_get($class, 'name.family', 'default'));
        $this->assertNull(ws_object_get($class, 'name.family'));
    }

    public function testObjectGetWhenKeyIsNullOrEmpty()
    {
        $object = new stdClass;

        $this->assertEquals($object, ws_object_get($object, null));
        $this->assertEquals($object, ws_object_get($object, false));
        $this->assertEquals($object, ws_object_get($object, ''));
        $this->assertEquals($object, ws_object_get($object, '  '));
    }

    public function testDataHas()
    {
        $object = (object) ['users' => ['name' => ['Taylor', 'Otwell']]];
        $array = [(object) ['users' => [(object) ['name' => 'Taylor']]]];
        $dottedArray = ['users' => ['first.name' => 'Taylor', 'middle.name' => null]];
        $arrayAccess = new SupportTestArrayAccess(['price' => 56, 'user' => new SupportTestArrayAccess(['name' => 'John']), 'email' => null]);
        $sameKeyMultiLevel = (object) ['name' => 'Taylor', 'company' => ['name' => 'Laravel']];
        $plainArray = [1, 2, 3];

        $this->assertTrue(ws_data_has($object, 'users.name.0'));
        $this->assertTrue(ws_data_has($array, '0.users.0.name'));
        $this->assertFalse(ws_data_has($array, '0.users.3'));
        $this->assertFalse(ws_data_has($array, '0.users.3'));
        $this->assertFalse(ws_data_has($array, '0.users.3'));
        $this->assertTrue(ws_data_has($dottedArray, ['users', 'first.name']));
        $this->assertTrue(ws_data_has($dottedArray, ['users', 'middle.name']));
        $this->assertFalse(ws_data_has($dottedArray, ['users', 'last.name']));
        $this->assertTrue(ws_data_has($arrayAccess, 'price'));
        $this->assertTrue(ws_data_has($arrayAccess, 'user.name'));
        $this->assertFalse(ws_data_has($arrayAccess, 'foo'));
        $this->assertFalse(ws_data_has($arrayAccess, 'user.foo'));
        $this->assertFalse(ws_data_has($arrayAccess, 'foo'));
        $this->assertFalse(ws_data_has($arrayAccess, 'user.foo'));
        $this->assertTrue(ws_data_has($arrayAccess, 'email'));
        $this->assertTrue(ws_data_has($sameKeyMultiLevel, 'name'));
        $this->assertTrue(ws_data_has($sameKeyMultiLevel, 'company.name'));
        $this->assertFalse(ws_data_has($sameKeyMultiLevel, 'foo.name'));
        $this->assertTrue(ws_data_has($plainArray, 0));
        $this->assertTrue(ws_data_has($plainArray, '0'));
        $this->assertFalse(ws_data_has($plainArray, 4));
        $this->assertFalse(ws_data_has($plainArray, '4'));
        $this->assertFalse(ws_data_has($plainArray, ''));
        $this->assertFalse(ws_data_has($plainArray, []));
        $this->assertFalse(ws_data_has($plainArray, null));
    }

    public function testDataGet()
    {
        $object = (object) ['users' => ['name' => ['Taylor', 'Otwell']]];
        $array = [(object) ['users' => [(object) ['name' => 'Taylor']]]];
        $dottedArray = ['users' => ['first.name' => 'Taylor', 'middle.name' => null]];
        $arrayAccess = new SupportTestArrayAccess(['price' => 56, 'user' => new SupportTestArrayAccess(['name' => 'John']), 'email' => null]);

        $this->assertSame('Taylor', ws_data_get($object, 'users.name.0'));
        $this->assertSame('Taylor', ws_data_get($array, '0.users.0.name'));
        $this->assertNull(ws_data_get($array, '0.users.3'));
        $this->assertSame('Not found', ws_data_get($array, '0.users.3', 'Not found'));
        $this->assertSame('Not found', ws_data_get($array, '0.users.3', function () {
            return 'Not found';
        }));
        $this->assertSame('Taylor', ws_data_get($dottedArray, ['users', 'first.name']));
        $this->assertNull(ws_data_get($dottedArray, ['users', 'middle.name']));
        $this->assertSame('Not found', ws_data_get($dottedArray, ['users', 'last.name'], 'Not found'));
        $this->assertEquals(56, ws_data_get($arrayAccess, 'price'));
        $this->assertSame('John', ws_data_get($arrayAccess, 'user.name'));
        $this->assertSame('void', ws_data_get($arrayAccess, 'foo', 'void'));
        $this->assertSame('void', ws_data_get($arrayAccess, 'user.foo', 'void'));
        $this->assertNull(ws_data_get($arrayAccess, 'foo'));
        $this->assertNull(ws_data_get($arrayAccess, 'user.foo'));
        $this->assertNull(ws_data_get($arrayAccess, 'email', 'Not found'));
    }

    public function testDataGetWithNestedArrays()
    {
        $array = [
            ['name' => 'taylor', 'email' => 'taylorotwell@gmail.com'],
            ['name' => 'abigail'],
            ['name' => 'dayle'],
        ];
        $arrayIterable = new SupportTestArrayIterable([
            ['name' => 'taylor', 'email' => 'taylorotwell@gmail.com'],
            ['name' => 'abigail'],
            ['name' => 'dayle'],
        ]);

        $this->assertEquals(['taylor', 'abigail', 'dayle'], ws_data_get($array, '*.name'));
        $this->assertEquals(['taylorotwell@gmail.com', null, null], ws_data_get($array, '*.email', 'irrelevant'));

        $this->assertEquals(['taylor', 'abigail', 'dayle'], ws_data_get($arrayIterable, '*.name'));
        $this->assertEquals(['taylorotwell@gmail.com', null, null], ws_data_get($arrayIterable, '*.email', 'irrelevant'));

        $array = [
            'users' => [
                ['first' => 'taylor', 'last' => 'otwell', 'email' => 'taylorotwell@gmail.com'],
                ['first' => 'abigail', 'last' => 'otwell'],
                ['first' => 'dayle', 'last' => 'rees'],
            ],
            'posts' => null,
        ];

        $this->assertEquals(['taylor', 'abigail', 'dayle'], ws_data_get($array, 'users.*.first'));
        $this->assertEquals(['taylorotwell@gmail.com', null, null], ws_data_get($array, 'users.*.email', 'irrelevant'));
        $this->assertSame('not found', ws_data_get($array, 'posts.*.date', 'not found'));
        $this->assertNull(ws_data_get($array, 'posts.*.date'));
    }

    public function testDataGetWithDoubleNestedArraysCollapsesResult()
    {
        $array = [
            'posts' => [
                [
                    'comments' => [
                        ['author' => 'taylor', 'likes' => 4],
                        ['author' => 'abigail', 'likes' => 3],
                    ],
                ],
                [
                    'comments' => [
                        ['author' => 'abigail', 'likes' => 2],
                        ['author' => 'dayle'],
                    ],
                ],
                [
                    'comments' => [
                        ['author' => 'dayle'],
                        ['author' => 'taylor', 'likes' => 1],
                    ],
                ],
            ],
        ];

        $this->assertEquals(['taylor', 'abigail', 'abigail', 'dayle', 'dayle', 'taylor'], ws_data_get($array, 'posts.*.comments.*.author'));
        $this->assertEquals([4, 3, 2, null, null, 1], ws_data_get($array, 'posts.*.comments.*.likes'));
        $this->assertEquals([], ws_data_get($array, 'posts.*.users.*.name', 'irrelevant'));
        $this->assertEquals([], ws_data_get($array, 'posts.*.users.*.name'));
    }

    public function testDataGetFirstLastDirectives()
    {
        $array = [
            'flights' => [
                [
                    'segments' => [
                        ['from' => 'LHR', 'departure' => '9:00', 'to' => 'IST', 'arrival' => '15:00'],
                        ['from' => 'IST', 'departure' => '16:00', 'to' => 'PKX', 'arrival' => '20:00'],
                    ],
                ],
                [
                    'segments' => [
                        ['from' => 'LGW', 'departure' => '8:00', 'to' => 'SAW', 'arrival' => '14:00'],
                        ['from' => 'SAW', 'departure' => '15:00', 'to' => 'PEK', 'arrival' => '19:00'],
                    ],
                ],
            ],
            'empty' => [],
        ];

        $this->assertEquals('LHR', ws_data_get($array, 'flights.0.segments.{first}.from'));
        $this->assertEquals('PKX', ws_data_get($array, 'flights.0.segments.{last}.to'));

        $this->assertEquals('LHR', ws_data_get($array, 'flights.{first}.segments.{first}.from'));
        $this->assertEquals('PEK', ws_data_get($array, 'flights.{last}.segments.{last}.to'));
        $this->assertEquals('PKX', ws_data_get($array, 'flights.{first}.segments.{last}.to'));
        $this->assertEquals('LGW', ws_data_get($array, 'flights.{last}.segments.{first}.from'));

        $this->assertEquals(['LHR', 'IST'], ws_data_get($array, 'flights.{first}.segments.*.from'));
        $this->assertEquals(['SAW', 'PEK'], ws_data_get($array, 'flights.{last}.segments.*.to'));

        $this->assertEquals(['LHR', 'LGW'], ws_data_get($array, 'flights.*.segments.{first}.from'));
        $this->assertEquals(['PKX', 'PEK'], ws_data_get($array, 'flights.*.segments.{last}.to'));

        $this->assertEquals('Not found', ws_data_get($array, 'empty.{first}', 'Not found'));
        $this->assertEquals('Not found', ws_data_get($array, 'empty.{last}', 'Not found'));
    }

    public function testDataGetFirstLastDirectivesOnArrayAccessIterable()
    {
        $arrayAccessIterable = [
            'flights' => new SupportTestArrayAccessIterable([
                [
                    'segments' => new SupportTestArrayAccessIterable([
                        ['from' => 'LHR', 'departure' => '9:00', 'to' => 'IST', 'arrival' => '15:00'],
                        ['from' => 'IST', 'departure' => '16:00', 'to' => 'PKX', 'arrival' => '20:00'],
                    ]),
                ],
                [
                    'segments' => new SupportTestArrayAccessIterable([
                        ['from' => 'LGW', 'departure' => '8:00', 'to' => 'SAW', 'arrival' => '14:00'],
                        ['from' => 'SAW', 'departure' => '15:00', 'to' => 'PEK', 'arrival' => '19:00'],
                    ]),
                ],
            ]),
            'empty' => new SupportTestArrayAccessIterable([]),
        ];

        $this->assertEquals('LHR', ws_data_get($arrayAccessIterable, 'flights.0.segments.{first}.from'));
        $this->assertEquals('PKX', ws_data_get($arrayAccessIterable, 'flights.0.segments.{last}.to'));

        $this->assertEquals('LHR', ws_data_get($arrayAccessIterable, 'flights.{first}.segments.{first}.from'));
        $this->assertEquals('PEK', ws_data_get($arrayAccessIterable, 'flights.{last}.segments.{last}.to'));
        $this->assertEquals('PKX', ws_data_get($arrayAccessIterable, 'flights.{first}.segments.{last}.to'));
        $this->assertEquals('LGW', ws_data_get($arrayAccessIterable, 'flights.{last}.segments.{first}.from'));

        $this->assertEquals(['LHR', 'IST'], ws_data_get($arrayAccessIterable, 'flights.{first}.segments.*.from'));
        $this->assertEquals(['SAW', 'PEK'], ws_data_get($arrayAccessIterable, 'flights.{last}.segments.*.to'));

        $this->assertEquals(['LHR', 'LGW'], ws_data_get($arrayAccessIterable, 'flights.*.segments.{first}.from'));
        $this->assertEquals(['PKX', 'PEK'], ws_data_get($arrayAccessIterable, 'flights.*.segments.{last}.to'));

        $this->assertEquals('Not found', ws_data_get($arrayAccessIterable, 'empty.{first}', 'Not found'));
        $this->assertEquals('Not found', ws_data_get($arrayAccessIterable, 'empty.{last}', 'Not found'));
    }

    public function testDataGetFirstLastDirectivesOnKeyedArrays()
    {
        $array = [
            'numericKeys' => [
                2 => 'first',
                0 => 'second',
                1 => 'last',
            ],
            'stringKeys' => [
                'one' => 'first',
                'two' => 'second',
                'three' => 'last',
            ],
        ];

        $this->assertEquals('second', ws_data_get($array, 'numericKeys.0'));
        $this->assertEquals('first', ws_data_get($array, 'numericKeys.{first}'));
        $this->assertEquals('last', ws_data_get($array, 'numericKeys.{last}'));
        $this->assertEquals('first', ws_data_get($array, 'stringKeys.{first}'));
        $this->assertEquals('last', ws_data_get($array, 'stringKeys.{last}'));
    }

    public function testDataGetEscapedSegmentKeys()
    {
        $array = [
            'symbols' => [
                '{last}' => ['description' => 'dollar'],
                '*' => ['description' => 'asterisk'],
                '{first}' => ['description' => 'caret'],
            ],
        ];

        $this->assertEquals('caret', ws_data_get($array, 'symbols.\{first}.description'));
        $this->assertEquals('dollar', ws_data_get($array, 'symbols.{first}.description'));
        $this->assertEquals('asterisk', ws_data_get($array, 'symbols.\*.description'));
        $this->assertEquals(['dollar', 'asterisk', 'caret'], ws_data_get($array, 'symbols.*.description'));
        $this->assertEquals('dollar', ws_data_get($array, 'symbols.\{last}.description'));
        $this->assertEquals('caret', ws_data_get($array, 'symbols.{last}.description'));
    }

    public function testDataGetStar()
    {
        $data = ['foo' => 'bar'];
        $this->assertEquals(['bar'], ws_data_get($data, '*'));

        $data = ws_collect(['foo' => 'bar']);
        $this->assertEquals(['bar'], ws_data_get($data, '*'));
    }

    public function testDataGetNullKey()
    {
        $data = ['foo' => 'bar'];

        $this->assertEquals(['foo' => 'bar'], ws_data_get($data, null));
        $this->assertEquals(['foo' => 'bar'], ws_data_get($data, null, '42'));
        $this->assertEquals(['foo' => 'bar'], ws_data_get($data, [null]));

        $data = ['foo' => 'bar', 'baz' => 42];
        $this->assertEquals(['foo' => 'bar', 'baz' => 42], ws_data_get($data, [null, 'foo']));
    }

    public function testDataFill()
    {
        $data = ['foo' => 'bar'];

        $this->assertEquals(['foo' => 'bar', 'baz' => 'boom'], ws_data_fill($data, 'baz', 'boom'));
        $this->assertEquals(['foo' => 'bar', 'baz' => 'boom'], ws_data_fill($data, 'baz', 'noop'));
        $this->assertEquals(['foo' => [], 'baz' => 'boom'], ws_data_fill($data, 'foo.*', 'noop'));
        $this->assertEquals(
            ['foo' => ['bar' => 'kaboom'], 'baz' => 'boom'],
            ws_data_fill($data, 'foo.bar', 'kaboom')
        );
    }

    public function testDataFillWithStar()
    {
        $data = ['foo' => 'bar'];

        $this->assertEquals(
            ['foo' => []],
            ws_data_fill($data, 'foo.*.bar', 'noop')
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => [['baz' => 'original'], []]],
            ws_data_fill($data, 'bar', [['baz' => 'original'], []])
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => [['baz' => 'original'], ['baz' => 'boom']]],
            ws_data_fill($data, 'bar.*.baz', 'boom')
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => [['baz' => 'original'], ['baz' => 'boom']]],
            ws_data_fill($data, 'bar.*', 'noop')
        );
    }

    public function testDataFillWithDoubleStar()
    {
        $data = [
            'posts' => [
                (object) [
                    'comments' => [
                        (object) ['name' => 'First'],
                        (object) [],
                    ],
                ],
                (object) [
                    'comments' => [
                        (object) [],
                        (object) ['name' => 'Second'],
                    ],
                ],
            ],
        ];

        ws_data_fill($data, 'posts.*.comments.*.name', 'Filled');

        $this->assertEquals([
            'posts' => [
                (object) [
                    'comments' => [
                        (object) ['name' => 'First'],
                        (object) ['name' => 'Filled'],
                    ],
                ],
                (object) [
                    'comments' => [
                        (object) ['name' => 'Filled'],
                        (object) ['name' => 'Second'],
                    ],
                ],
            ],
        ], $data);
    }

    public function testDataSet()
    {
        $data = ['foo' => 'bar'];

        $this->assertEquals(
            ['foo' => 'bar', 'baz' => 'boom'],
            ws_data_set($data, 'baz', 'boom')
        );

        $this->assertEquals(
            ['foo' => 'bar', 'baz' => 'kaboom'],
            ws_data_set($data, 'baz', 'kaboom')
        );

        $this->assertEquals(
            ['foo' => [], 'baz' => 'kaboom'],
            ws_data_set($data, 'foo.*', 'noop')
        );

        $this->assertEquals(
            ['foo' => ['bar' => 'boom'], 'baz' => 'kaboom'],
            ws_data_set($data, 'foo.bar', 'boom')
        );

        $this->assertEquals(
            ['foo' => ['bar' => 'boom'], 'baz' => ['bar' => 'boom']],
            ws_data_set($data, 'baz.bar', 'boom')
        );

        $this->assertEquals(
            ['foo' => ['bar' => 'boom'], 'baz' => ['bar' => ['boom' => ['kaboom' => 'boom']]]],
            ws_data_set($data, 'baz.bar.boom.kaboom', 'boom')
        );
    }

    public function testDataSetWithStar()
    {
        $data = ['foo' => 'bar'];

        $this->assertEquals(
            ['foo' => []],
            ws_data_set($data, 'foo.*.bar', 'noop')
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => [['baz' => 'original'], []]],
            ws_data_set($data, 'bar', [['baz' => 'original'], []])
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => [['baz' => 'boom'], ['baz' => 'boom']]],
            ws_data_set($data, 'bar.*.baz', 'boom')
        );

        $this->assertEquals(
            ['foo' => [], 'bar' => ['overwritten', 'overwritten']],
            ws_data_set($data, 'bar.*', 'overwritten')
        );
    }

    public function testDataSetWithDoubleStar()
    {
        $data = [
            'posts' => [
                (object) [
                    'comments' => [
                        (object) ['name' => 'First'],
                        (object) [],
                    ],
                ],
                (object) [
                    'comments' => [
                        (object) [],
                        (object) ['name' => 'Second'],
                    ],
                ],
            ],
        ];

        ws_data_set($data, 'posts.*.comments.*.name', 'Filled');

        $this->assertEquals([
            'posts' => [
                (object) [
                    'comments' => [
                        (object) ['name' => 'Filled'],
                        (object) ['name' => 'Filled'],
                    ],
                ],
                (object) [
                    'comments' => [
                        (object) ['name' => 'Filled'],
                        (object) ['name' => 'Filled'],
                    ],
                ],
            ],
        ], $data);
    }

    public function testDataRemove()
    {
        $data = ['foo' => 'bar', 'hello' => 'world'];

        $this->assertEquals(
            ['hello' => 'world'],
            ws_data_forget($data, 'foo')
        );

        $data = ['foo' => 'bar', 'hello' => 'world'];

        $this->assertEquals(
            ['foo' => 'bar', 'hello' => 'world'],
            ws_data_forget($data, 'nothing')
        );

        $data = ['one' => ['two' => ['three' => 'hello', 'four' => ['five']]]];

        $this->assertEquals(
            ['one' => ['two' => ['four' => ['five']]]],
            ws_data_forget($data, 'one.two.three')
        );
    }

    public function testDataRemoveWithStar()
    {
        $data = [
            'article' => [
                'title' => 'Foo',
                'comments' => [
                    ['comment' => 'foo', 'name' => 'First'],
                    ['comment' => 'bar', 'name' => 'Second'],
                ],
            ],
        ];

        $this->assertEquals(
            [
                'article' => [
                    'title' => 'Foo',
                    'comments' => [
                        ['comment' => 'foo'],
                        ['comment' => 'bar'],
                    ],
                ],
            ],
            ws_data_forget($data, 'article.comments.*.name')
        );
    }

    public function testDataRemoveWithDoubleStar()
    {
        $data = [
            'posts' => [
                (object) [
                    'comments' => [
                        (object) ['name' => 'First', 'comment' => 'foo'],
                        (object) ['name' => 'Second', 'comment' => 'bar'],
                    ],
                ],
                (object) [
                    'comments' => [
                        (object) ['name' => 'Third', 'comment' => 'hello'],
                        (object) ['name' => 'Fourth', 'comment' => 'world'],
                    ],
                ],
            ],
        ];

        ws_data_forget($data, 'posts.*.comments.*.name');

        $this->assertEquals([
            'posts' => [
                (object) [
                    'comments' => [
                        (object) ['comment' => 'foo'],
                        (object) ['comment' => 'bar'],
                    ],
                ],
                (object) [
                    'comments' => [
                        (object) ['comment' => 'hello'],
                        (object) ['comment' => 'world'],
                    ],
                ],
            ],
        ], $data);
    }

    public function testHead()
    {
        $array = ['a', 'b', 'c'];
        $this->assertSame('a', ws_head($array));
    }

    public function testLast()
    {
        $array = ['a', 'b', 'c'];
        $this->assertSame('c', ws_last($array));
    }

    public function testClassUsesRecursiveShouldReturnTraitsOnParentClasses()
    {
        $this->assertSame(
            [
                SupportTestTraitTwo::class => SupportTestTraitTwo::class,
                SupportTestTraitOne::class => SupportTestTraitOne::class,
            ],
            ws_class_uses_recursive(SupportTestClassTwo::class)
        );
    }

    public function testClassUsesRecursiveAcceptsObject()
    {
        $this->assertSame(
            [
                SupportTestTraitTwo::class => SupportTestTraitTwo::class,
                SupportTestTraitOne::class => SupportTestTraitOne::class,
            ],
            ws_class_uses_recursive(new SupportTestClassTwo)
        );
    }

    public function testClassUsesRecursiveReturnParentTraitsFirst()
    {
        $this->assertSame(
            [
                SupportTestTraitTwo::class => SupportTestTraitTwo::class,
                SupportTestTraitOne::class => SupportTestTraitOne::class,
                SupportTestTraitThree::class => SupportTestTraitThree::class,
            ],
            ws_class_uses_recursive(SupportTestClassThree::class)
        );
    }

    public function testTraitUsesRecursive()
    {
        $this->assertSame(
            [
                'WpStarter\Tests\Support\SupportTestTraitTwo' => 'WpStarter\Tests\Support\SupportTestTraitTwo',
                'WpStarter\Tests\Support\SupportTestTraitOne' => 'WpStarter\Tests\Support\SupportTestTraitOne',
            ],
            ws_trait_uses_recursive(SupportTestClassOne::class)
        );

        $this->assertSame([], ws_trait_uses_recursive(SupportTestClassTwo::class));
    }

    public function testStr()
    {
        $stringable = ws_str('string-value');

        $this->assertInstanceOf(Stringable::class, $stringable);
        $this->assertSame('string-value', (string) $stringable);

        $stringable = ws_str($name = null);
        $this->assertInstanceOf(Stringable::class, $stringable);
        $this->assertTrue($stringable->isEmpty());

        $strAccessor = ws_str();
        $this->assertTrue((new ReflectionClass($strAccessor))->isAnonymous());
        $this->assertSame($strAccessor->limit('string-value', 3), 'str...');

        $strAccessor = ws_str();
        $this->assertTrue((new ReflectionClass($strAccessor))->isAnonymous());
        $this->assertSame((string) $strAccessor, '');
    }

    public function testTap()
    {
        $object = (object) ['id' => 1];
        $this->assertEquals(2, ws_tap($object, function ($object) {
            $object->id = 2;
        })->id);

        $mock = m::mock();
        $mock->shouldReceive('foo')->once()->andReturn('bar');
        $this->assertEquals($mock, ws_tap($mock)->foo());
    }

    public function testThrow()
    {
        $this->expectException(LogicException::class);

        ws_throw_if(true, new LogicException);
    }

    public function testThrowDefaultException()
    {
        $this->expectException(RuntimeException::class);

        ws_throw_if(true);
    }

    public function testThrowExceptionWithMessage()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('test');

        ws_throw_if(true, 'test');
    }

    public function testThrowExceptionAsStringWithMessage()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('test');

        ws_throw_if(true, LogicException::class, 'test');
    }

    public function testThrowClosureException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('test');

        ws_throw_if(true, fn () => new \Exception('test'));
    }

    public function testThrowClosureWithParamsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('test');

        ws_throw_if(true, fn (string $message) => new \Exception($message), 'test');
    }

    public function testThrowClosureStringWithParamsException()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('test');

        ws_throw_if(true, fn () => \Exception::class, 'test');
    }

    public function testThrowUnless()
    {
        $this->expectException(LogicException::class);

        ws_throw_unless(false, new LogicException);
    }

    public function testThrowUnlessDefaultException()
    {
        $this->expectException(RuntimeException::class);

        ws_throw_unless(false);
    }

    public function testThrowUnlessExceptionWithMessage()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('test');

        ws_throw_unless(false, 'test');
    }

    public function testThrowUnlessExceptionAsStringWithMessage()
    {
        $this->expectException(LogicException::class);
        $this->expectExceptionMessage('test');

        ws_throw_unless(false, LogicException::class, 'test');
    }

    public function testThrowReturnIfNotThrown()
    {
        $this->assertSame('foo', ws_throw_unless('foo', new RuntimeException));
    }

    public function testThrowWithString()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Test Message');

        ws_throw_if(true, RuntimeException::class, 'Test Message');
    }

    public function testOptional()
    {
        $this->assertNull(ws_optional(null)->something());

        $this->assertEquals(10, ws_optional(new class
        {
            public function something()
            {
                return 10;
            }
        })->something());
    }

    public function testOptionalWithCallback()
    {
        $this->assertNull(ws_optional(null, function () {
            throw new RuntimeException(
                'The optional callback should not be called for null'
            );
        }));

        $this->assertEquals(10, ws_optional(5, function ($number) {
            return $number * 2;
        }));
    }

    public function testOptionalWithArray()
    {
        $this->assertSame('here', ws_optional(['present' => 'here'])['present']);
        $this->assertNull(ws_optional(null)['missing']);
        $this->assertNull(ws_optional(['present' => 'here'])->missing);
    }

    public function testOptionalReturnsObjectPropertyOrNull()
    {
        $this->assertSame('bar', ws_optional((object) ['foo' => 'bar'])->foo);
        $this->assertNull(ws_optional(['foo' => 'bar'])->foo);
        $this->assertNull(ws_optional((object) ['foo' => 'bar'])->bar);
    }

    public function testOptionalDeterminesWhetherKeyIsSet()
    {
        $this->assertTrue(isset(ws_optional(['foo' => 'bar'])['foo']));
        $this->assertFalse(isset(ws_optional(['foo' => 'bar'])['bar']));
        $this->assertFalse(isset(ws_optional()['bar']));
    }

    public function testOptionalAllowsToSetKey()
    {
        $optional = ws_optional([]);
        $optional['foo'] = 'bar';
        $this->assertSame('bar', $optional['foo']);

        $optional = ws_optional(null);
        $optional['foo'] = 'bar';
        $this->assertFalse(isset($optional['foo']));
    }

    public function testOptionalAllowToUnsetKey()
    {
        $optional = ws_optional(['foo' => 'bar']);
        $this->assertTrue(isset($optional['foo']));
        unset($optional['foo']);
        $this->assertFalse(isset($optional['foo']));

        $optional = ws_optional((object) ['foo' => 'bar']);
        $this->assertFalse(isset($optional['foo']));
        $optional['foo'] = 'bar';
        $this->assertFalse(isset($optional['foo']));
    }

    public function testOptionalIsMacroable()
    {
        Optional::macro('present', function () {
            if (is_object($this->value)) {
                return $this->value->present();
            }

            return new Optional(null);
        });

        $this->assertNull(ws_optional(null)->present()->something());

        $this->assertSame('$10.00', ws_optional(new class
        {
            public function present()
            {
                return new class
                {
                    public function something()
                    {
                        return '$10.00';
                    }
                };
            }
        })->present()->something());
    }

    public function testRetry()
    {
        Sleep::fake();

        $attempts = ws_retry(2, function ($attempts) {
            if ($attempts > 1) {
                return $attempts;
            }

            throw new RuntimeException;
        }, 100);

        // Make sure we made two attempts
        $this->assertEquals(2, $attempts);

        // Make sure we waited 100ms for the first attempt
        Sleep::assertSleptTimes(1);

        Sleep::assertSequence([
            Sleep::usleep(100_000),
        ]);
    }

    public function testRetryWithCarbonIntervalSleep()
    {
        Sleep::fake();

        $attempts = ws_retry(2, function ($attempts) {
            if ($attempts > 1) {
                return $attempts;
            }

            throw new RuntimeException;
        }, CarbonInterval::milliseconds(100));

        // Make sure we made two attempts
        $this->assertEquals(2, $attempts);

        // Make sure we waited 100ms for the first attempt
        Sleep::assertSleptTimes(1);

        Sleep::assertSequence([
            Sleep::usleep(100_000),
        ]);
    }

    public function testRetryWithPassingSleepCallback()
    {
        Sleep::fake();

        $attempts = ws_retry(3, function ($attempts) {
            if ($attempts > 2) {
                return $attempts;
            }

            throw new RuntimeException;
        }, function ($attempt, $exception) {
            $this->assertInstanceOf(RuntimeException::class, $exception);

            return $attempt * 100;
        });

        // Make sure we made three attempts
        $this->assertEquals(3, $attempts);

        // Make sure we waited 300ms for the first two attempts
        Sleep::assertSleptTimes(2);

        Sleep::assertSequence([
            Sleep::usleep(100_000),
            Sleep::usleep(200_000),
        ]);
    }

    public function testRetryWithPassingWhenCallback()
    {
        Sleep::fake();

        $attempts = ws_retry(2, function ($attempts) {
            if ($attempts > 1) {
                return $attempts;
            }

            throw new RuntimeException;
        }, 100, function ($ex) {
            return true;
        });

        // Make sure we made two attempts
        $this->assertEquals(2, $attempts);

        // Make sure we waited 100ms for the first attempt
        Sleep::assertSleptTimes(1);

        Sleep::assertSequence([
            Sleep::usleep(100_000),
        ]);
    }

    public function testRetryWithFailingWhenCallback()
    {
        $this->expectException(RuntimeException::class);

        ws_retry(2, function ($attempts) {
            if ($attempts > 1) {
                return $attempts;
            }

            throw new RuntimeException;
        }, 100, function ($ex) {
            return false;
        });
    }

    public function testRetryWithBackoff()
    {
        Sleep::fake();

        $attempts = ws_retry([50, 100, 200], function ($attempts) {
            if ($attempts > 3) {
                return $attempts;
            }

            throw new RuntimeException;
        });

        // Make sure we made four attempts
        $this->assertEquals(4, $attempts);

        Sleep::assertSleptTimes(3);

        Sleep::assertSequence([
            Sleep::usleep(50_000),
            Sleep::usleep(100_000),
            Sleep::usleep(200_000),
        ]);
    }

    public function testRetryWithAThrowableBase()
    {
        Sleep::fake();

        $attempts = ws_retry(2, function ($attempts) {
            if ($attempts > 1) {
                return $attempts;
            }

            throw new Error('This is an error');
        }, 100);

        // Make sure we made two attempts
        $this->assertEquals(2, $attempts);

        // Make sure we waited 100ms for the first attempt
        Sleep::assertSleptTimes(1);

        Sleep::assertSequence([
            Sleep::usleep(100_000),
        ]);
    }

    public function testTransform()
    {
        $this->assertEquals(10, ws_transform(5, function ($value) {
            return $value * 2;
        }));

        $this->assertNull(ws_transform(null, function () {
            return 10;
        }));
    }

    public function testTransformDefaultWhenBlank()
    {
        $this->assertSame('baz', ws_transform(null, function () {
            return 'bar';
        }, 'baz'));

        $this->assertSame('baz', ws_transform('', function () {
            return 'bar';
        }, function () {
            return 'baz';
        }));
    }

    public function testWith()
    {
        $this->assertEquals(10, ws_with(10));

        $this->assertEquals(10, ws_with(5, function ($five) {
            return $five + 5;
        }));
    }

    public function testAppendConfig()
    {
        $this->assertSame([10000 => 'name', 10001 => 'family'], ws_append_config([1 => 'name', 2 => 'family']));
        $this->assertSame([10000 => 'name', 10001 => 'family'], ws_append_config(['name', 'family']));

        $array = ['name' => 'Taylor', 'family' => 'Otwell'];
        $this->assertSame($array, ws_append_config($array));
    }

    public function testEnv()
    {
        $_SERVER['foo'] = 'bar';
        $this->assertSame('bar', ws_env('foo'));
        $this->assertSame('bar', Env::get('foo'));
    }

    public function testEnvTrue()
    {
        $_SERVER['foo'] = 'true';
        $this->assertTrue(ws_env('foo'));

        $_SERVER['foo'] = '(true)';
        $this->assertTrue(ws_env('foo'));
    }

    public function testEnvFalse()
    {
        $_SERVER['foo'] = 'false';
        $this->assertFalse(ws_env('foo'));

        $_SERVER['foo'] = '(false)';
        $this->assertFalse(ws_env('foo'));
    }

    public function testEnvEmpty()
    {
        $_SERVER['foo'] = '';
        $this->assertSame('', ws_env('foo'));

        $_SERVER['foo'] = 'empty';
        $this->assertSame('', ws_env('foo'));

        $_SERVER['foo'] = '(empty)';
        $this->assertSame('', ws_env('foo'));
    }

    public function testEnvNull()
    {
        $_SERVER['foo'] = 'null';
        $this->assertNull(ws_env('foo'));

        $_SERVER['foo'] = '(null)';
        $this->assertNull(ws_env('foo'));
    }

    public function testEnvDefault()
    {
        $_SERVER['foo'] = 'bar';
        $this->assertSame('bar', ws_env('foo', 'default'));

        $_SERVER['foo'] = '';
        $this->assertSame('', ws_env('foo', 'default'));

        unset($_SERVER['foo']);
        $this->assertSame('default', ws_env('foo', 'default'));

        $_SERVER['foo'] = null;
        $this->assertSame('default', ws_env('foo', 'default'));
    }

    public function testEnvEscapedString()
    {
        $_SERVER['foo'] = '"null"';
        $this->assertSame('null', ws_env('foo'));

        $_SERVER['foo'] = "'null'";
        $this->assertSame('null', ws_env('foo'));

        $_SERVER['foo'] = 'x"null"x'; // this should not be unquoted
        $this->assertSame('x"null"x', ws_env('foo'));
    }

    public function testWriteArrayOfEnvVariablesToFile()
    {
        $filesystem = new Filesystem;
        $path = __DIR__.'/tmp/env-test-file';
        $filesystem->put($path, implode(PHP_EOL, [
            'APP_NAME=Laravel',
            'APP_ENV=local',
            'APP_KEY=base64:randomkey',
            'APP_DEBUG=true',
            'APP_URL=http://localhost',
            '',
            'DB_CONNECTION=mysql',
            'DB_HOST=',
        ]));

        Env::writeVariables([
            'APP_VIBE' => 'chill',
            'DB_HOST' => '127:0:0:1',
            'DB_PORT' => 3306,
            'BRAND_NEW_PREFIX' => 'fresh value',
        ], $path);

        $this->assertSame(
            implode(PHP_EOL, [
                'APP_NAME=Laravel',
                'APP_ENV=local',
                'APP_KEY=base64:randomkey',
                'APP_DEBUG=true',
                'APP_URL=http://localhost',
                'APP_VIBE=chill',
                '',
                'DB_CONNECTION=mysql',
                'DB_HOST="127:0:0:1"',
                'DB_PORT=3306',
                '',
                'BRAND_NEW_PREFIX="fresh value"',
            ]),
            $filesystem->get($path)
        );
    }

    public function testWriteArrayOfEnvVariablesToFileAndOverwrite()
    {
        $filesystem = new Filesystem;
        $path = __DIR__.'/tmp/env-test-file';
        $filesystem->put($path, implode(PHP_EOL, [
            'APP_NAME=Laravel',
            'APP_ENV=local',
            'APP_KEY=base64:randomkey',
            'APP_DEBUG=true',
            'APP_URL=http://localhost',
            '',
            'DB_CONNECTION=mysql',
            'DB_HOST=',
        ]));

        Env::writeVariables([
            'APP_VIBE' => 'chill',
            'DB_HOST' => '127:0:0:1',
            'DB_CONNECTION' => 'sqlite',
        ], $path, true);

        $this->assertSame(
            implode(PHP_EOL, [
                'APP_NAME=Laravel',
                'APP_ENV=local',
                'APP_KEY=base64:randomkey',
                'APP_DEBUG=true',
                'APP_URL=http://localhost',
                'APP_VIBE=chill',
                '',
                'DB_CONNECTION=sqlite',
                'DB_HOST="127:0:0:1"',
            ]),
            $filesystem->get($path)
        );
    }

    public function testWillNotOverwriteArrayOfVariables()
    {
        $filesystem = new Filesystem;
        $path = __DIR__.'/tmp/env-test-file';
        $filesystem->put($path, implode(PHP_EOL, [
            'APP_NAME=Laravel',
            'APP_ENV=local',
            'APP_KEY=base64:randomkey',
            'APP_DEBUG=true',
            'APP_URL=http://localhost',
            'APP_VIBE=odd',
            '',
            'DB_CONNECTION=mysql',
            'DB_HOST=',
        ]));

        Env::writeVariables([
            'APP_VIBE' => 'chill',
            'DB_HOST' => '127:0:0:1',
        ], $path);

        $this->assertSame(
            implode(PHP_EOL, [
                'APP_NAME=Laravel',
                'APP_ENV=local',
                'APP_KEY=base64:randomkey',
                'APP_DEBUG=true',
                'APP_URL=http://localhost',
                'APP_VIBE=odd',
                '',
                'DB_CONNECTION=mysql',
                'DB_HOST="127:0:0:1"',
            ]),
            $filesystem->get($path)
        );
    }

    public function testWriteVariableToFile()
    {
        $filesystem = new Filesystem;
        $path = __DIR__.'/tmp/env-test-file';
        $filesystem->put($path, implode(PHP_EOL, [
            'APP_NAME=Laravel',
            'APP_ENV=local',
            'APP_KEY=base64:randomkey',
            'APP_DEBUG=true',
            'APP_URL=http://localhost',
            '',
            'DB_CONNECTION=mysql',
            'DB_HOST=',
        ]));

        Env::writeVariable('APP_VIBE', 'chill', $path);

        $this->assertSame(
            implode(PHP_EOL, [
                'APP_NAME=Laravel',
                'APP_ENV=local',
                'APP_KEY=base64:randomkey',
                'APP_DEBUG=true',
                'APP_URL=http://localhost',
                'APP_VIBE=chill',
                '',
                'DB_CONNECTION=mysql',
                'DB_HOST=',
            ]),
            $filesystem->get($path)
        );
    }

    public function testWillNotOverwriteVariable()
    {
        $filesystem = new Filesystem;
        $path = __DIR__.'/tmp/env-test-file';
        $filesystem->put($path, implode(PHP_EOL, [
            'APP_NAME=Laravel',
            'APP_ENV=local',
            'APP_KEY=base64:randomkey',
            'APP_DEBUG=true',
            'APP_URL=http://localhost',
            'APP_VIBE=odd',
            '',
            'DB_CONNECTION=mysql',
            'DB_HOST=',
        ]));

        Env::writeVariable('APP_VIBE', 'chill', $path);

        $this->assertSame(
            implode(PHP_EOL, [
                'APP_NAME=Laravel',
                'APP_ENV=local',
                'APP_KEY=base64:randomkey',
                'APP_DEBUG=true',
                'APP_URL=http://localhost',
                'APP_VIBE=odd',
                '',
                'DB_CONNECTION=mysql',
                'DB_HOST=',
            ]),
            $filesystem->get($path)
        );
    }

    public function testWriteVariableToFileAndOverwrite()
    {
        $filesystem = new Filesystem;
        $path = __DIR__.'/tmp/env-test-file';
        $filesystem->put($path, implode(PHP_EOL, [
            'APP_NAME=Laravel',
            'APP_ENV=local',
            'APP_KEY=base64:randomkey',
            'APP_DEBUG=true',
            'APP_URL=http://localhost',
            'APP_VIBE=odd',
            '',
            'DB_CONNECTION=mysql',
            'DB_HOST=',
        ]));

        Env::writeVariable('APP_VIBE', 'chill', $path, true);

        $this->assertSame(
            implode(PHP_EOL, [
                'APP_NAME=Laravel',
                'APP_ENV=local',
                'APP_KEY=base64:randomkey',
                'APP_DEBUG=true',
                'APP_URL=http://localhost',
                'APP_VIBE=chill',
                '',
                'DB_CONNECTION=mysql',
                'DB_HOST=',
            ]),
            $filesystem->get($path)
        );
    }

    public function testWriteVariableQuotesValuesWithSpecialCharacters()
    {
        $filesystem = new Filesystem;
        $path = __DIR__.'/tmp/env-test-file';
        $filesystem->put($path, 'APP_NAME=Laravel'.PHP_EOL);

        Env::writeVariable('APP_BRACKET', 'pass[word', $path);
        Env::writeVariable('APP_CARET', 'foo^bar', $path);
        Env::writeVariable('APP_BACKTICK', 'foo`bar', $path);

        $contents = $filesystem->get($path);

        $this->assertStringContainsString('APP_BRACKET="pass[word"', $contents);
        $this->assertStringContainsString('APP_CARET="foo^bar"', $contents);
        $this->assertStringContainsString('APP_BACKTICK="foo`bar"', $contents);
    }

    public function testWillThrowAnExceptionIfFileIsMissingWhenTryingToWriteVariables(): void
    {
        $this->expectExceptionObject(new RuntimeException('The file [missing-file] does not exist.'));

        Env::writeVariables([
            'APP_VIBE' => 'chill',
            'DB_HOST' => '127:0:0:1',
        ], 'missing-file');
    }

    public function testGetFromSERVERFirst()
    {
        $_ENV['foo'] = 'From $_ENV';
        $_SERVER['foo'] = 'From $_SERVER';
        $this->assertSame('From $_SERVER', ws_env('foo'));
    }

    public function testRequiredEnvVariableThrowsAnExceptionWhenNotFound(): void
    {
        $this->expectExceptionObject(new RuntimeException('[required-does-not-exist] has no value'));

        Env::getOrFail('required-does-not-exist');
    }

    public function testRequiredEnvReturnsValue(): void
    {
        $_SERVER['required-exists'] = 'some-value';
        $this->assertSame('some-value', Env::getOrFail('required-exists'));
    }

    public function testLiteral(): void
    {
        $this->assertEquals(1, ws_literal(1));
        $this->assertEquals('taylor', ws_literal('taylor'));
        $this->assertEquals((object) ['name' => 'Taylor', 'role' => 'Developer'], ws_literal(name: 'Taylor', role: 'Developer'));
    }

    public static function providesPregReplaceArrayData()
    {
        $pointerArray = ['Taylor', 'Otwell'];

        next($pointerArray);

        return [
            ['/:[a-z_]+/', ['8:30', '9:00'], 'The event will take place between :start and :end', 'The event will take place between 8:30 and 9:00'],
            ['/%s/', ['Taylor'], 'Hi, %s', 'Hi, Taylor'],
            ['/%s/', ['Taylor', 'Otwell'], 'Hi, %s %s', 'Hi, Taylor Otwell'],
            ['/%s/', [], 'Hi, %s %s', 'Hi,  '],
            ['/%s/', ['a', 'b', 'c'], 'Hi', 'Hi'],
            ['//', [], '', ''],
            ['/%s/', ['a'], '', ''],
            // non-sequential numeric keys → should still consume in natural order
            ['/%s/', [2 => 'A', 10 => 'B'], '%s %s', 'A B'],
            // associative keys → order should be insertion order, not keys/pointer
            ['/%s/', ['first' => 'A', 'second' => 'B'], '%s %s', 'A B'],
            // values that are "falsy" but must not be treated as empty by mistake, false->'' , null->''
            ['/%s/', ['0', 0, false, null], '%s|%s|%s|%s', '0|0||'],
            // The internal pointer of this array is not at the beginning
            ['/%s/', $pointerArray, 'Hi, %s %s', 'Hi, Taylor Otwell'],
        ];
    }

    #[DataProvider('providesPregReplaceArrayData')]
    public function testPregReplaceArray($pattern, $replacements, $subject, $expectedOutput)
    {
        $this->assertSame(
            $expectedOutput,
            ws_preg_replace_array($pattern, $replacements, $subject)
        );
    }

    public function testLazy(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(SupportLazyClass::class, function (SupportLazyClass $instance) {
            $instance->__construct('foo', 'bar');
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testLazyCanAcceptShortClosure(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(SupportLazyClass::class, fn (SupportLazyClass $instance) => $instance->__construct('foo', 'bar'));

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testLazyThrowsExceptionWhenConstructorIsNotCalled()
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        $instance = ws_lazy(SupportLazyClass::class, function (SupportLazyClass $instance) {
            //
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);

        $this->expectException(Error::class);
        $this->expectExceptionMessage('Typed property WpStarter\Tests\Support\SupportLazyClass::$first must not be accessed before initialization');

        $instance->first;
    }

    public function testLazyCanAcceptHashForProperties(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(SupportLazyClass::class, fn (SupportLazyClass $instance) => [
            'second' => 'bar',
            'first' => 'foo',
        ]);

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testLazyCanAcceptListForProperties(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(SupportLazyClass::class, fn (SupportLazyClass $instance) => ['foo', 'bar']);

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testLazyCanAcceptSingleValueForConstructor(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClassWithArrayParameter::$constructorCalled = false;

        $instance = ws_lazy(SupportLazyClassWithArrayParameter::class, fn (SupportLazyClassWithArrayParameter $instance) => [['foo']]);

        $this->assertFalse(SupportLazyClassWithArrayParameter::$constructorCalled);
        $this->assertSame(['foo'], $instance->first);
        $this->assertTrue(SupportLazyClassWithArrayParameter::$constructorCalled);

        SupportLazyClassWithArrayParameter::$constructorCalled = false;
    }

    public function testLazySupportsPositionAndNamedArguments(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(SupportLazyClass::class, fn (SupportLazyClass $instance) => ['foo', 'second' => 'bar']);

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testLazyThrowsWhenPositionalArgumentsComeAfterNamedArguments(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(SupportLazyClass::class, fn (SupportLazyClass $instance) => ['second' => 'bar', 'foo']);

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Cannot use positional argument after named argument during unpacking');

        $instance->first;
    }

    public function testLazyCanReturnInitializedObject(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(SupportLazyClass::class, function (SupportLazyClass $instance) {
            $instance->__construct('foo');

            return $instance;
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertNull($instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testLazyMustInitilizeObject(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(SupportLazyClass::class, function (SupportLazyClass $instance) {
            return new SupportLazyClass('foo');
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Typed property WpStarter\Tests\Support\SupportLazyClass::$first must not be accessed before initialization');

        $instance->first;
    }

    public function testLazyCanEagerlySetProperties(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(SupportLazyClass::class, fn () => ['foo', 'bar'], eager: ['eager' => 'baz']);

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('baz', $instance->eager);
        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('baz', $instance->eager);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testClosureOnlyLazy(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(function (SupportLazyClass $instance) {
            $instance->__construct('foo', 'bar');
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testClosureOnlyLazyCanAcceptShortClosure(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(fn (SupportLazyClass $instance) => $instance->__construct('foo', 'bar'));

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testClosureOnlyLazyThrowsExceptionWhenConstructorIsNotCalled()
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        $instance = ws_lazy(function (SupportLazyClass $instance) {
            //
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);

        $this->expectException(Error::class);
        $this->expectExceptionMessage('Typed property WpStarter\Tests\Support\SupportLazyClass::$first must not be accessed before initialization');

        $instance->first;
    }

    public function testClosureOnlyLazyThrowsWhenNotClassSpecifiedInClosure()
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The first parameter of the given Closure is missing a type hint.');

        ws_lazy(function ($instance) {
            //
        });
    }

    public function testClosureOnlyLazyCanAcceptHashForProperties(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(fn (SupportLazyClass $instance) => [
            'second' => 'bar',
            'first' => 'foo',
        ]);

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testClosureOnlyLazyCanAcceptListForProperties(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(fn (SupportLazyClass $instance) => ['foo', 'bar']);

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testClousureOnlyLazyCanAcceptSingleValueForConstructor(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClassWithArrayParameter::$constructorCalled = false;

        $instance = ws_lazy(fn (SupportLazyClassWithArrayParameter $instance) => [['foo']]);

        $this->assertFalse(SupportLazyClassWithArrayParameter::$constructorCalled);
        $this->assertSame(['foo'], $instance->first);
        $this->assertTrue(SupportLazyClassWithArrayParameter::$constructorCalled);

        SupportLazyClassWithArrayParameter::$constructorCalled = false;
    }

    public function testClosureOnlyLazySupportsPositionAndNamedArguments(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(fn (SupportLazyClass $instance) => ['foo', 'second' => 'bar']);

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testClosureOnlyLazyThrowsWhenPositionalArgumentsComeAfterNamedArguments(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(fn (SupportLazyClass $instance) => ['second' => 'bar', 'foo']);

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Cannot use positional argument after named argument during unpacking');

        $instance->first;
    }

    public function testClosureOnlyLazyCanReturnInitializedObject(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(function (SupportLazyClass $instance) {
            $instance->__construct('foo');

            return $instance;
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertNull($instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testClosureOnlyLazyMustInitilizeObject(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_lazy(function (SupportLazyClass $instance) {
            return new SupportLazyClass('foo');
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Typed property WpStarter\Tests\Support\SupportLazyClass::$first must not be accessed before initialization');

        $instance->first;
    }

    public function testProxy(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;
        $factory = fn () => new SupportLazyClass('foo', 'bar');

        $instance = ws_proxy(SupportLazyClass::class, fn (SupportLazyClass $proxy) => $factory());

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testProxyCanEagerlySetProperties(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;
        $factory = fn () => new SupportLazyClass('foo', 'bar');

        $instance = ws_proxy(SupportLazyClass::class, fn () => $factory(), eager: ['eager' => 'baz']);

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('baz', $instance->eager);
        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertFalse(isset($instance->eager));

        SupportLazyClass::$constructorCalled = false;
    }

    public function testProxyCanEagerlySetPropertiesAndThenAlsoSetThemOnActualObject(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;
        $factory = fn () => new SupportLazyClass('foo', 'bar');

        $instance = ws_proxy(SupportLazyClass::class, function ($proxy, $eager) use ($factory) {
            $instance = $factory();

            foreach ($eager as $prop => $value) {
                $instance->{$prop} = $value;
            }

            return $instance;
        }, eager: ['eager' => 'baz']);

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('baz', $instance->eager);
        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('baz', $instance->eager);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testProxyCanAcceptShortClosure(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;
        $factory = fn () => new SupportLazyClass('foo', 'bar');

        $instance = ws_proxy(SupportLazyClass::class, fn (SupportLazyClass $proxy) => $factory());

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testProxyThrowsExceptionWhenObjectIsNotReturned()
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        $instance = ws_proxy(SupportLazyClass::class, function (SupportLazyClass $proxy) {
            //
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);

        $this->expectException(Error::class);
        $this->expectExceptionMessage('Lazy proxy factory must return an instance of a class compatible with WpStarter\Tests\Support\SupportLazyClass, null returned');

        $instance->first;
    }

    public function testProxyMustNotInitilizeProxy(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_proxy(SupportLazyClass::class, function (SupportLazyClass $proxy) {
            $proxy->__construct('foo');

            return $proxy;
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Lazy proxy factory must return a non-lazy object');

        $instance->first;
    }

    public function testClosureOnlyProxy(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;
        $factory = fn () => new SupportLazyClass('foo', 'bar');

        $instance = ws_proxy(function (SupportLazyClass $proxy) use ($factory) {
            return $factory();
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testClosureOnlyProxyCanAcceptShortClosure(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;
        $factory = fn () => new SupportLazyClass('foo', 'bar');

        $instance = ws_proxy(fn (SupportLazyClass $proxy) => $factory());

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }

    public function testClosureOnlyProxyThrowsExceptionWhenObjectIsNotReturned()
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        $instance = ws_proxy(function (SupportLazyClass $proxy) {
            //
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);

        $this->expectException(Error::class);
        $this->expectExceptionMessage('Lazy proxy factory must return an instance of a class compatible with WpStarter\Tests\Support\SupportLazyClass, null returned');

        $instance->first;
    }

    public function testClosureOnlyProxyThrowsWhenNotClassSpecifiedInClosure()
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('The first parameter of the given Closure is missing a type hint.');

        ws_proxy(function ($proxy) {
            //
        });
    }

    public function testClosureOnlyProxyMustNotInitilizeProxy(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;

        $instance = ws_proxy(function (SupportLazyClass $proxy) {
            $proxy->__construct('foo');

            return $proxy;
        });

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->expectException(Error::class);
        $this->expectExceptionMessage('Lazy proxy factory must return a non-lazy object');

        $instance->first;
    }

    public function testProxyCanUseClosureReturnTypeForClassDetection(): void
    {
        if (version_compare(phpversion(), '8.4.0', '<')) {
            $this->markTestSkipped();
        }

        SupportLazyClass::$constructorCalled = false;
        $factory = fn () => new SupportLazyClass('foo', 'bar');

        $instance = ws_proxy(fn (): SupportLazyClass => $factory());

        $this->assertFalse(SupportLazyClass::$constructorCalled);
        $this->assertSame('foo', $instance->first);
        $this->assertTrue(SupportLazyClass::$constructorCalled);
        $this->assertSame('bar', $instance->second);

        SupportLazyClass::$constructorCalled = false;
    }
}

trait SupportTestTraitOne
{
    //
}

trait SupportTestTraitTwo
{
    use SupportTestTraitOne;
}

class SupportTestClassOne
{
    use SupportTestTraitTwo;
}

class SupportTestClassTwo extends SupportTestClassOne
{
    //
}

trait SupportTestTraitThree
{
    //
}

class SupportTestClassThree extends SupportTestClassTwo
{
    use SupportTestTraitThree;
}

trait SupportTestTraitArrayAccess
{
    public function __construct(protected array $items = [])
    {
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset ?? '', $this->items);
    }

    public function offsetGet($offset): mixed
    {
        return $this->items[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->items[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->items[$offset]);
    }
}

trait SupportTestTraitArrayIterable
{
    public function __construct(protected array $items = [])
    {
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->items);
    }
}

class SupportTestArrayAccess implements ArrayAccess
{
    use SupportTestTraitArrayAccess;
}

class SupportTestArrayIterable implements IteratorAggregate
{
    use SupportTestTraitArrayIterable;
}

class SupportTestArrayAccessIterable implements ArrayAccess, IteratorAggregate
{
    use SupportTestTraitArrayAccess, SupportTestTraitArrayIterable {
        SupportTestTraitArrayAccess::__construct insteadof SupportTestTraitArrayIterable;
    }
}

class SupportTestCountable implements Countable
{
    public function count(): int
    {
        return 0;
    }
}

class SupportLazyClass
{
    public static bool $constructorCalled = false;

    public string $eager;

    public function __construct(
        public string $first,
        public ?string $second = null,
    ) {
        self::$constructorCalled = true;
    }
}

class SupportLazyClassWithArrayParameter
{
    public static bool $constructorCalled = false;

    public function __construct(
        public array $first,
    ) {
        self::$constructorCalled = true;
    }
}
