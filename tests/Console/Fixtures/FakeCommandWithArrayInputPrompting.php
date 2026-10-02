<?php

namespace WpStarter\Tests\Console\Fixtures;

use WpStarter\Console\Command;
use WpStarter\Contracts\Console\PromptsForMissingInput;
use Laravel\Prompts\Prompt;
use Laravel\Prompts\TextPrompt;
use Symfony\Component\Console\Input\InputInterface;

class FakeCommandWithArrayInputPrompting extends Command implements PromptsForMissingInput
{
    protected $signature = 'fake-command-for-testing-array {names* : An array argument}';

    public $prompted = false;

    protected function configurePrompts(InputInterface $input)
    {
        Prompt::interactive(true);
        Prompt::fallbackWhen(true);

        TextPrompt::fallbackUsing(function () {
            $this->prompted = true;

            return 'foo';
        });
    }

    public function handle(): int
    {
        return self::SUCCESS;
    }
}
