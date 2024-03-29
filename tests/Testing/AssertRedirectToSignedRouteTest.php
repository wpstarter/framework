<?php

namespace WpStarter\Tests\Testing;

use WpStarter\Contracts\Routing\Registrar;
use WpStarter\Http\RedirectResponse;
use WpStarter\Routing\UrlGenerator;
use WpStarter\Support\Facades\Facade;
use Orchestra\Testbench\TestCase;

class AssertRedirectToSignedRouteTest extends TestCase
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
            ->get('signed-route')
            ->name('signed-route');

        $this->router
            ->get('signed-route-with-param/{param}')
            ->name('signed-route-with-param');

        $this->urlGenerator = $this->app->make(UrlGenerator::class);
    }

    public function testAssertRedirectToSignedRouteWithoutRouteName()
    {
        $this->router->get('test-route', function () {
            return new RedirectResponse($this->urlGenerator->signedRoute('signed-route'));
        });

        $this->get('test-route')
            ->assertRedirectToSignedRoute();
    }

    public function testAssertRedirectToSignedRouteWithRouteName()
    {
        $this->router->get('test-route', function () {
            return new RedirectResponse($this->urlGenerator->signedRoute('signed-route'));
        });

        $this->get('test-route')
            ->assertRedirectToSignedRoute('signed-route');
    }

    public function testAssertRedirectToSignedRouteWithRouteNameAndParams()
    {
        $this->router->get('test-route', function () {
            return new RedirectResponse($this->urlGenerator->signedRoute('signed-route-with-param', 'hello'));
        });

        $this->router->get('test-route-with-extra-param', function () {
            return new RedirectResponse($this->urlGenerator->signedRoute('signed-route-with-param', [
                'param' => 'foo',
                'extra' => 'another',
            ]));
        });

        $this->get('test-route')
            ->assertRedirectToSignedRoute('signed-route-with-param', 'hello');

        $this->get('test-route-with-extra-param')
            ->assertRedirectToSignedRoute('signed-route-with-param', [
                'param' => 'foo',
                'extra' => 'another',
            ]);
    }

    public function testAssertRedirectToSignedRouteWithRouteNameToTemporarySignedRoute()
    {
        $this->router->get('test-route', function () {
            return new RedirectResponse($this->urlGenerator->temporarySignedRoute('signed-route', 60));
        });

        $this->get('test-route')
            ->assertRedirectToSignedRoute('signed-route');
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Facade::setFacadeApplication(null);
    }
}
