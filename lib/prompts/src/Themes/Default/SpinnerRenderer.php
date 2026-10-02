<?php

namespace WpStarter\Prompts\Themes\Default;

use WpStarter\Prompts\Concerns\HasSpinner;
use WpStarter\Prompts\Spinner;

class SpinnerRenderer extends Renderer
{
    use HasSpinner;

    /**
     * Render the spinner.
     */
    public function __invoke(Spinner $spinner): string
    {
        if ($spinner->static) {
            return $this->line(" {$this->cyan($this->staticFrame)} {$spinner->message}");
        }

        $spinner->interval = $this->interval;

        return $this->line(" {$this->cyan($this->spinnerFrame($spinner->count))} {$spinner->message}");
    }
}
