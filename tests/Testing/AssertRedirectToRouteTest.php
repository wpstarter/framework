<?php

namespace WpStarter\Tests\Testing;

use WpStarter\Contracts\Routing\Registrar;
use WpStarter\Http\RedirectResponse;
use WpStarter\Routing\UrlGenerator;
use WpStarter\Support\Facades\Facade;
use Orchestra\Testbench\TestCase;

class AssertRedirectToRouteTest extends TestCase
{
    /**
     * @var \WpStarter\Contracts\Routing\Registrar
     */
    private $router;

    /**
     * @var \WpStarter\Routing\UrlGenerator
     */
    private $urlGenerator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = $this->app->make(Registrar::class);

        $this->router
            ->get('named-route')
            ->name('named-route');

        $this->router
            ->get('named-route-with-param/{param}')
            ->name('named-route-with-param');

        $this->router
            ->get('')
            ->name('route-with-empty-uri');

        $this->urlGenerator = $this->app->make(UrlGenerator::class);
    }

    public function testAssertRedirectToRouteWithRouteName(): void
    {
        $this->router->get('test-route', function () {
            return new RedirectResponse($this->urlGenerator->route('named-route'));
        });

        $this->get('test-route')
            ->assertRedirectToRoute('named-route');
    }

    public function testAssertRedirectToRouteWithRouteNameAndParams(): void
    {
        $this->router->get('test-route', function () {
            return new RedirectResponse($this->urlGenerator->route('named-route-with-param', 'hello'));
        });

        $this->router->get('test-route-with-extra-param', function () {
            return new RedirectResponse($this->urlGenerator->route('named-route-with-param', [
                'param' => 'foo',
                'extra' => 'another',
            ]));
        });

        $this->get('test-route')
            ->assertRedirectToRoute('named-route-with-param', 'hello');

        $this->get('test-route-with-extra-param')
            ->assertRedirectToRoute('named-route-with-param', [
                'param' => 'foo',
                'extra' => 'another',
            ]);
    }

    public function testAssertRedirectToRouteWithRouteNameAndParamsWhenRouteUriIsEmpty(): void
    {
        $this->router->get('test-route', function () {
            return new RedirectResponse($this->urlGenerator->route('route-with-empty-uri', ['foo' => 'bar']));
        });

        $this->get('test-route')
            ->assertRedirectToRoute('route-with-empty-uri', ['foo' => 'bar']);
    }

    protected function tearDown(): void
    {
        Facade::setFacadeApplication(null);

        parent::tearDown();
    }
}
