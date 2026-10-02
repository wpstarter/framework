<?php

namespace WpStarter\Tests\Validation;

use WpStarter\Auth\Access\Gate;
use WpStarter\Container\Container;
use WpStarter\Contracts\Auth\Access\Gate as GateContract;
use WpStarter\Support\Facades\Facade;
use WpStarter\Translation\ArrayLoader;
use WpStarter\Translation\Translator;
use WpStarter\Validation\Rules\Can;
use WpStarter\Validation\ValidationServiceProvider;
use WpStarter\Validation\Validator;
use PHPUnit\Framework\TestCase;
use stdClass;

class ValidationRuleCanTest extends TestCase
{
    protected $container;
    protected $user;
    protected $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = new stdClass;

        Container::setInstance($this->container = new Container);

        $this->container->singleton(GateContract::class, function () {
            return new Gate($this->container, function () {
                return $this->user;
            });
        });

        $this->container->bind('translator', function () {
            return new Translator(
                new ArrayLoader, 'en'
            );
        });

        Facade::setFacadeApplication($this->container);

        (new ValidationServiceProvider($this->container))->register();
    }

    protected function tearDown(): void
    {
        Container::setInstance(null);

        Facade::clearResolvedInstances();

        Facade::setFacadeApplication(null);

        parent::tearDown();
    }

    public function testValidationFails()
    {
        $this->gate()->define('update-company', function ($user, $value) {
            $this->assertEquals('1', $value);

            return false;
        });

        $v = new Validator(
            ws_resolve('translator'),
            ['company' => '1'],
            ['company' => new Can('update-company')]
        );

        $this->assertTrue($v->fails());
    }

    public function testValidationPasses()
    {
        $this->gate()->define('update-company', function ($user, $class, $model, $value) {
            $this->assertEquals(\App\Models\Company::class, $class);
            $this->assertInstanceOf(stdClass::class, $model);
            $this->assertEquals('1', $value);

            return true;
        });

        $v = new Validator(
            ws_resolve('translator'),
            ['company' => '1'],
            ['company' => new Can('update-company', [\App\Models\Company::class, new stdClass])]
        );

        $this->assertTrue($v->passes());
    }

    public function testCustomMessageUsingDotNotationAndFqcnWorks()
    {
        $v = new Validator(
            ws_resolve('translator'),
            [
                'company' => '1',
                'company_fqcn' => '1',
            ],
            [
                'company' => new Can('update-company', [\App\Models\Company::class, new stdClass]),
                'company_fqcn' => new Can('update-company', [\App\Models\Company::class, new stdClass]),
            ],
            [
                'company.can' => 'You dont have permission (dot notation)',
                'company_fqcn.WpStarter\Validation\Rules\Can' => 'You dont have permission (fqcn)',
            ]
        );

        $this->assertTrue($v->fails());

        $this->assertSame([
            'You dont have permission (dot notation)',
            'You dont have permission (fqcn)',
        ], $v->messages()->all());
    }

    /**
     * Get the Gate instance from the container.
     *
     * @return \WpStarter\Auth\Access\Gate
     */
    protected function gate()
    {
        return $this->container->make(GateContract::class);
    }
}
