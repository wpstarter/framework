<?php

namespace WpStarter\Tests\Support;

use ArrayAccess;
use WpStarter\Contracts\Support\Htmlable;
use WpStarter\Support\Env;
use WpStarter\Support\Optional;
use LogicException;
use Mockery as m;
use PHPUnit\Framework\TestCase;
use RuntimeException;
use stdClass;

class SupportHelpersTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testE()
    {
        $str = 'A \'quote\' is <b>bold</b>';
        $this->assertSame('A &#039;quote&#039; is &lt;b&gt;bold&lt;/b&gt;', ws_e($str));
        $html = m::mock(Htmlable::class);
        $html->shouldReceive('toHtml')->andReturn($str);
        $this->assertEquals($str, ws_e($html));
    }

    public function testClassBasename()
    {
        $this->assertSame('Baz', ws_class_basename('Foo\Bar\Baz'));
        $this->assertSame('Baz', ws_class_basename('Baz'));
    }

    public function testValue()
    {
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

        $this->assertEquals(['taylor', 'abigail', 'dayle'], ws_data_get($array, '*.name'));
        $this->assertEquals(['taylorotwell@gmail.com', null, null], ws_data_get($array, '*.email', 'irrelevant'));

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
        $this->assertSame([
            SupportTestTraitTwo::class => SupportTestTraitTwo::class,
            SupportTestTraitOne::class => SupportTestTraitOne::class,
        ],
        ws_class_uses_recursive(SupportTestClassTwo::class));
    }

    public function testClassUsesRecursiveAcceptsObject()
    {
        $this->assertSame([
            SupportTestTraitTwo::class => SupportTestTraitTwo::class,
            SupportTestTraitOne::class => SupportTestTraitOne::class,
        ],
        ws_class_uses_recursive(new SupportTestClassTwo));
    }

    public function testClassUsesRecursiveReturnParentTraitsFirst()
    {
        $this->assertSame([
            SupportTestTraitTwo::class => SupportTestTraitTwo::class,
            SupportTestTraitOne::class => SupportTestTraitOne::class,
            SupportTestTraitThree::class => SupportTestTraitThree::class,
        ],
        ws_class_uses_recursive(SupportTestClassThree::class));
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
        $startTime = microtime(true);

        $attempts = ws_retry(2, function ($attempts) {
            if ($attempts > 1) {
                return $attempts;
            }

            throw new RuntimeException;
        }, 100);

        // Make sure we made two attempts
        $this->assertEquals(2, $attempts);

        // Make sure we waited 100ms for the first attempt
        $this->assertEqualsWithDelta(0.1, microtime(true) - $startTime, 0.03);
    }

    public function testRetryWithPassingSleepCallback()
    {
        $startTime = microtime(true);

        $attempts = ws_retry(3, function ($attempts) {
            if ($attempts > 2) {
                return $attempts;
            }

            throw new RuntimeException;
        }, function ($attempt) {
            return $attempt * 100;
        });

        // Make sure we made three attempts
        $this->assertEquals(3, $attempts);

        // Make sure we waited 300ms for the first two attempts
        $this->assertEqualsWithDelta(0.3, microtime(true) - $startTime, 0.03);
    }

    public function testRetryWithPassingWhenCallback()
    {
        $startTime = microtime(true);

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
        $this->assertEqualsWithDelta(0.1, microtime(true) - $startTime, 0.03);
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

    public function testGetFromSERVERFirst()
    {
        $_ENV['foo'] = 'From $_ENV';
        $_SERVER['foo'] = 'From $_SERVER';
        $this->assertSame('From $_SERVER', ws_env('foo'));
    }

    public function providesPregReplaceArrayData()
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
            // The internal pointer of this array is not at the beginning
            ['/%s/', $pointerArray, 'Hi, %s %s', 'Hi, Taylor Otwell'],
        ];
    }

    /**
     * @dataProvider providesPregReplaceArrayData
     */
    public function testPregReplaceArray($pattern, $replacements, $subject, $expectedOutput)
    {
        $this->assertSame(
            $expectedOutput,
            ws_preg_replace_array($pattern, $replacements, $subject)
        );
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

class SupportTestArrayAccess implements ArrayAccess
{
    protected $attributes = [];

    public function __construct($attributes = [])
    {
        $this->attributes = $attributes;
    }

    public function offsetExists($offset): bool
    {
        return array_key_exists($offset, $this->attributes);
    }

    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return $this->attributes[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->attributes[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset]);
    }
}
