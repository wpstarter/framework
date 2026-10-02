<?php

namespace WpStarter\Tests\Integration\Queue\Fixtures\Jobs;

use WpStarter\Contracts\Queue\ShouldQueue;
use WpStarter\Foundation\Auth\User;
use WpStarter\Foundation\Queue\Queueable;

class DeleteUser implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user
    ) {
        log($user);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->user->delete();
    }
}
