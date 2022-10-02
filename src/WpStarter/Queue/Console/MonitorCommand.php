<?php

namespace WpStarter\Queue\Console;

use WpStarter\Console\Command;
use WpStarter\Contracts\Events\Dispatcher;
use WpStarter\Contracts\Queue\Factory;
use WpStarter\Queue\Events\QueueBusy;
use WpStarter\Support\Collection;

class MonitorCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'queue:monitor
                       {queues : The names of the queues to monitor}
                       {--max=1000 : The maximum number of jobs that can be on the queue before an event is dispatched}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Monitor the size of the specified queues';

    /**
     * The queue manager instance.
     *
     * @var \WpStarter\Contracts\Queue\Factory
     */
    protected $manager;

    /**
     * The events dispatcher instance.
     *
     * @var \WpStarter\Contracts\Events\Dispatcher
     */
    protected $events;

    /**
     * The table headers for the command.
     *
     * @var string[]
     */
    protected $headers = ['Connection', 'Queue', 'Size', 'Status'];

    /**
     * Create a new queue listen command.
     *
     * @param  \WpStarter\Contracts\Queue\Factory  $manager
     * @param  \WpStarter\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function __construct(Factory $manager, Dispatcher $events)
    {
        parent::__construct();

        $this->manager = $manager;
        $this->events = $events;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $queues = $this->parseQueues($this->argument('queues'));

        $this->displaySizes($queues);

        $this->dispatchEvents($queues);
    }

    /**
     * Parse the queues into an array of the connections and queues.
     *
     * @param  string  $queues
     * @return \WpStarter\Support\Collection
     */
    protected function parseQueues($queues)
    {
        return ws_collect(explode(',', $queues))->map(function ($queue) {
            [$connection, $queue] = array_pad(explode(':', $queue, 2), 2, null);

            if (! isset($queue)) {
                $queue = $connection;
                $connection = $this->laravel['config']['queue.default'];
            }

            return [
                'connection' => $connection,
                'queue' => $queue,
                'size' => $size = $this->manager->connection($connection)->size($queue),
                'status' => $size >= $this->option('max') ? '<fg=red>ALERT</>' : 'OK',
            ];
        });
    }

    /**
     * Display the failed jobs in the console.
     *
     * @param  \WpStarter\Support\Collection  $queues
     * @return void
     */
    protected function displaySizes(Collection $queues)
    {
        $this->table($this->headers, $queues);
    }

    /**
     * Fire the monitoring events.
     *
     * @param  \WpStarter\Support\Collection  $queues
     * @return void
     */
    protected function dispatchEvents(Collection $queues)
    {
        foreach ($queues as $queue) {
            if ($queue['status'] == 'OK') {
                continue;
            }

            $this->events->dispatch(
                new QueueBusy(
                    $queue['connection'],
                    $queue['queue'],
                    $queue['size'],
                )
            );
        }
    }
}
