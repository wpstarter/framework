<?php

namespace WpStarter\Testing;

use WpStarter\Support\Traits\Macroable;
use WpStarter\Testing\Assert as PHPUnit;
use WpStarter\Testing\Constraints\SeeInOrder;
use WpStarter\View\View;

class TestView
{
    use Macroable;

    /**
     * The original view.
     *
     * @var \WpStarter\View\View
     */
    protected $view;

    /**
     * The rendered view contents.
     *
     * @var string
     */
    protected $rendered;

    /**
     * Create a new test view instance.
     *
     * @param  \WpStarter\View\View  $view
     * @return void
     */
    public function __construct(View $view)
    {
        $this->view = $view;
        $this->rendered = $view->render();
    }

    /**
     * Assert that the given string is contained within the view.
     *
     * @param  string  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertSee($value, $escape = true)
    {
        $value = $escape ? ws_e($value) : $value;

        PHPUnit::assertStringContainsString((string) $value, $this->rendered);

        return $this;
    }

    /**
     * Assert that the given strings are contained in order within the view.
     *
     * @param  array  $values
     * @param  bool  $escape
     * @return $this
     */
    public function assertSeeInOrder(array $values, $escape = true)
    {
        $values = $escape ? array_map('ws_e', ($values)) : $values;

        PHPUnit::assertThat($values, new SeeInOrder($this->rendered));

        return $this;
    }

    /**
     * Assert that the given string is contained within the view text.
     *
     * @param  string  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertSeeText($value, $escape = true)
    {
        $value = $escape ? ws_e($value) : $value;

        PHPUnit::assertStringContainsString((string) $value, strip_tags($this->rendered));

        return $this;
    }

    /**
     * Assert that the given strings are contained in order within the view text.
     *
     * @param  array  $values
     * @param  bool  $escape
     * @return $this
     */
    public function assertSeeTextInOrder(array $values, $escape = true)
    {
        $values = $escape ? array_map('ws_e', ($values)) : $values;

        PHPUnit::assertThat($values, new SeeInOrder(strip_tags($this->rendered)));

        return $this;
    }

    /**
     * Assert that the given string is not contained within the view.
     *
     * @param  string  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertDontSee($value, $escape = true)
    {
        $value = $escape ? ws_e($value) : $value;

        PHPUnit::assertStringNotContainsString((string) $value, $this->rendered);

        return $this;
    }

    /**
     * Assert that the given string is not contained within the view text.
     *
     * @param  string  $value
     * @param  bool  $escape
     * @return $this
     */
    public function assertDontSeeText($value, $escape = true)
    {
        $value = $escape ? ws_e($value) : $value;

        PHPUnit::assertStringNotContainsString((string) $value, strip_tags($this->rendered));

        return $this;
    }

    /**
     * Get the string contents of the rendered view.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->rendered;
    }
}
