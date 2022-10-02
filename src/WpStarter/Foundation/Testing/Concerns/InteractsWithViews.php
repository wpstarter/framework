<?php

namespace WpStarter\Foundation\Testing\Concerns;

use WpStarter\Support\Facades\View as ViewFacade;
use WpStarter\Support\MessageBag;
use WpStarter\Support\ViewErrorBag;
use WpStarter\Testing\TestComponent;
use WpStarter\Testing\TestView;
use WpStarter\View\View;

trait InteractsWithViews
{
    /**
     * Create a new TestView from the given view.
     *
     * @param  string  $view
     * @param  \WpStarter\Contracts\Support\Arrayable|array  $data
     * @return \WpStarter\Testing\TestView
     */
    protected function view(string $view, array $data = [])
    {
        return new TestView(ws_view($view, $data));
    }

    /**
     * Render the contents of the given Blade template string.
     *
     * @param  string  $template
     * @param  \WpStarter\Contracts\Support\Arrayable|array  $data
     * @return \WpStarter\Testing\TestView
     */
    protected function blade(string $template, array $data = [])
    {
        $tempDirectory = sys_get_temp_dir();

        if (! in_array($tempDirectory, ViewFacade::getFinder()->getPaths())) {
            ViewFacade::addLocation(sys_get_temp_dir());
        }

        $tempFileInfo = pathinfo(tempnam($tempDirectory, 'laravel-blade'));

        $tempFile = $tempFileInfo['dirname'].'/'.$tempFileInfo['filename'].'.blade.php';

        file_put_contents($tempFile, $template);

        return new TestView(ws_view($tempFileInfo['filename'], $data));
    }

    /**
     * Render the given view component.
     *
     * @param  string  $componentClass
     * @param  \WpStarter\Contracts\Support\Arrayable|array  $data
     * @return \WpStarter\Testing\TestComponent
     */
    protected function component(string $componentClass, array $data = [])
    {
        $component = $this->app->make($componentClass, $data);

        $view = ws_value($component->resolveView(), $data);

        $view = $view instanceof View
            ? $view->with($component->data())
            : ws_view($view, $component->data());

        return new TestComponent($component, $view);
    }

    /**
     * Populate the shared view error bag with the given errors.
     *
     * @param  array  $errors
     * @param  string  $key
     * @return $this
     */
    protected function withViewErrors(array $errors, $key = 'default')
    {
        ViewFacade::share('errors', (new ViewErrorBag)->put($key, new MessageBag($errors)));

        return $this;
    }
}
