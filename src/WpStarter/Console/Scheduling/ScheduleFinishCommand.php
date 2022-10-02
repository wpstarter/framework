<?php

namespace WpStarter\Console\Scheduling;

use WpStarter\Console\Command;
use WpStarter\Console\Events\ScheduledBackgroundTaskFinished;
use WpStarter\Contracts\Events\Dispatcher;

class ScheduleFinishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'schedule:finish {id} {code=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Handle the completion of a scheduled command';

    /**
     * Indicates whether the command should be shown in the Artisan command list.
     *
     * @var bool
     */
    protected $hidden = true;

    /**
     * Execute the console command.
     *
     * @param  \WpStarter\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    public function handle(Schedule $schedule)
    {
        ws_collect($schedule->events())->filter(function ($value) {
            return $value->mutexName() == $this->argument('id');
        })->each(function ($event) {
            $event->callafterCallbacksWithExitCode($this->laravel, $this->argument('code'));

            $this->laravel->make(Dispatcher::class)->dispatch(new ScheduledBackgroundTaskFinished($event));
        });
    }
}
