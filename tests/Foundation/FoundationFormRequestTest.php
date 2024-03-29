<?php

namespace WpStarter\Tests\Foundation;

use Exception;
use WpStarter\Auth\Access\AuthorizationException;
use WpStarter\Auth\Access\Response;
use WpStarter\Container\Container;
use WpStarter\Contracts\Translation\Translator;
use WpStarter\Contracts\Validation\Factory as ValidationFactoryContract;
use WpStarter\Contracts\Validation\Validator;
use WpStarter\Foundation\Http\FormRequest;
use WpStarter\Http\RedirectResponse;
use WpStarter\Routing\Redirector;
use WpStarter\Routing\UrlGenerator;
use WpStarter\Validation\Factory as ValidationFactory;
use WpStarter\Validation\ValidationException;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class FoundationFormRequestTest extends TestCase
{
    protected $mocks = [];

    protected function tearDown(): void
    {
        m::close();

        $this->mocks = [];
    }

    public function testValidatedMethodReturnsTheValidatedData()
    {
        $request = $this->createRequest(['name' => 'specified', 'with' => 'extras']);

        $request->validateResolved();

        $this->assertEquals(['name' => 'specified'], $request->validated());
    }

    public function testValidatedMethodReturnsTheValidatedDataNestedRules()
    {
        $payload = ['nested' => ['foo' => 'bar', 'baz' => ''], 'array' => [1, 2]];

        $request = $this->createRequest($payload, FoundationTestFormRequestNestedStub::class);

        $request->validateResolved();

        $this->assertEquals(['nested' => ['foo' => 'bar'], 'array' => [1, 2]], $request->validated());
    }

    public function testValidatedMethodReturnsTheValidatedDataNestedChildRules()
    {
        $payload = ['nested' => ['foo' => 'bar', 'with' => 'extras']];

        $request = $this->createRequest($payload, FoundationTestFormRequestNestedChildStub::class);

        $request->validateResolved();

        $this->assertEquals(['nested' => ['foo' => 'bar']], $request->validated());
    }

    public function testValidatedMethodReturnsTheValidatedDataNestedArrayRules()
    {
        $payload = ['nested' => [['bar' => 'baz', 'with' => 'extras'], ['bar' => 'baz2', 'with' => 'extras']]];

        $request = $this->createRequest($payload, FoundationTestFormRequestNestedArrayStub::class);

        $request->validateResolved();

        $this->assertEquals(['nested' => [['bar' => 'baz'], ['bar' => 'baz2']]], $request->validated());
    }

    public function testValidatedMethodNotValidateTwice()
    {
        $payload = ['name' => 'specified', 'with' => 'extras'];

        $request = $this->createRequest($payload, FoundationTestFormRequestTwiceStub::class);

        $request->validateResolved();
        $request->validated();

        $this->assertEquals(1, FoundationTestFormRequestTwiceStub::$count);
    }

    public function testValidateThrowsWhenValidationFails()
    {
        $this->expectException(ValidationException::class);

        $request = $this->createRequest(['no' => 'name']);

        $this->mocks['redirect']->shouldReceive('withInput->withErrors');

        $request->validateResolved();
    }

    public function testValidateMethodThrowsWhenAuthorizationFails()
    {
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('This action is unauthorized.');

        $this->createRequest([], FoundationTestFormRequestForbiddenStub::class)->validateResolved();
    }

    public function testValidateThrowsExceptionFromAuthorizationResponse()
    {
        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('foo');

        $this->createRequest([], FoundationTestFormRequestForbiddenWithResponseStub::class)->validateResolved();
    }

    public function testValidateDoesntThrowExceptionFromResponseAllowed()
    {
        $this->createRequest([], FoundationTestFormRequestPassesWithResponseStub::class)->validateResolved();
    }

    public function testPrepareForValidationRunsBeforeValidation()
    {
        $this->createRequest([], FoundationTestFormRequestHooks::class)->validateResolved();
    }

    public function test_after_validation_runs_after_validation()
    {
        $request = $this->createRequest([], FoundationTestFormRequestHooks::class);

        $request->validateResolved();

        $this->assertEquals(['name' => 'Adam'], $request->all());
    }

    /**
     * Catch the given exception thrown from the executor, and return it.
     *
     * @param  string  $class
     * @param  \Closure  $executor
     * @return \Exception
     *
     * @throws \Exception
     */
    protected function catchException($class, $executor)
    {
        try {
            $executor();
        } catch (Exception $e) {
            if (is_a($e, $class)) {
                return $e;
            }

            throw $e;
        }

        throw new Exception("No exception thrown. Expected exception {$class}.");
    }

    /**
     * Create a new request of the given type.
     *
     * @param  array  $payload
     * @param  string  $class
     * @return \WpStarter\Foundation\Http\FormRequest
     */
    protected function createRequest($payload = [], $class = FoundationTestFormRequestStub::class)
    {
        $container = ws_tap(new Container, function ($container) {
            $container->instance(
                ValidationFactoryContract::class,
                $this->createValidationFactory($container)
            );
        });

        $request = $class::create('/', 'GET', $payload);

        return $request->setRedirector($this->createMockRedirector($request))
                       ->setContainer($container);
    }

    /**
     * Create a new validation factory.
     *
     * @param  \WpStarter\Container\Container  $container
     * @return \WpStarter\Validation\Factory
     */
    protected function createValidationFactory($container)
    {
        $translator = m::mock(Translator::class)->shouldReceive('get')
                       ->zeroOrMoreTimes()->andReturn('error')->getMock();

        return new ValidationFactory($translator, $container);
    }

    /**
     * Create a mock redirector.
     *
     * @param  \WpStarter\Http\Request  $request
     * @return \WpStarter\Routing\Redirector
     */
    protected function createMockRedirector($request)
    {
        $redirector = $this->mocks['redirector'] = m::mock(Redirector::class);

        $redirector->shouldReceive('getUrlGenerator')->zeroOrMoreTimes()
                   ->andReturn($generator = $this->createMockUrlGenerator());

        $redirector->shouldReceive('to')->zeroOrMoreTimes()
                   ->andReturn($this->createMockRedirectResponse());

        $generator->shouldReceive('previous')->zeroOrMoreTimes()
                  ->andReturn('previous');

        return $redirector;
    }

    /**
     * Create a mock URL generator.
     *
     * @return \WpStarter\Routing\UrlGenerator
     */
    protected function createMockUrlGenerator()
    {
        return $this->mocks['generator'] = m::mock(UrlGenerator::class);
    }

    /**
     * Create a mock redirect response.
     *
     * @return \WpStarter\Http\RedirectResponse
     */
    protected function createMockRedirectResponse()
    {
        return $this->mocks['redirect'] = m::mock(RedirectResponse::class);
    }
}

class FoundationTestFormRequestStub extends FormRequest
{
    public function rules()
    {
        return ['name' => 'required'];
    }

    public function authorize()
    {
        return true;
    }
}

class FoundationTestFormRequestNestedStub extends FormRequest
{
    public function rules()
    {
        return ['nested.foo' => 'required', 'array.*' => 'integer'];
    }

    public function authorize()
    {
        return true;
    }
}

class FoundationTestFormRequestNestedChildStub extends FormRequest
{
    public function rules()
    {
        return ['nested.foo' => 'required'];
    }

    public function authorize()
    {
        return true;
    }
}

class FoundationTestFormRequestNestedArrayStub extends FormRequest
{
    public function rules()
    {
        return ['nested.*.bar' => 'required'];
    }

    public function authorize()
    {
        return true;
    }
}

class FoundationTestFormRequestTwiceStub extends FormRequest
{
    public static $count = 0;

    public function rules()
    {
        return ['name' => 'required'];
    }

    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            self::$count++;
        });
    }

    public function authorize()
    {
        return true;
    }
}

class FoundationTestFormRequestForbiddenStub extends FormRequest
{
    public function authorize()
    {
        return false;
    }
}

class FoundationTestFormRequestHooks extends FormRequest
{
    public function rules()
    {
        return ['name' => 'required'];
    }

    public function authorize()
    {
        return true;
    }

    public function prepareForValidation()
    {
        $this->replace(['name' => 'Taylor']);
    }

    public function passedValidation()
    {
        $this->replace(['name' => 'Adam']);
    }
}

class FoundationTestFormRequestForbiddenWithResponseStub extends FormRequest
{
    public function authorize()
    {
        return Response::deny('foo');
    }
}

class FoundationTestFormRequestPassesWithResponseStub extends FormRequest
{
    public function rules()
    {
        return [];
    }

    public function authorize()
    {
        return Response::allow('baz');
    }
}
