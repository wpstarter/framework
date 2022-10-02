<?php

namespace WpStarter\Queue\Middleware;

use WpStarter\Container\Container;
use WpStarter\Contracts\Redis\Factory as Redis;
use WpStarter\Redis\Limiters\DurationLimiter;
use WpStarter\Support\InteractsWithTime;
use Throwable;

class ThrottlesExceptionsWithRedis extends ThrottlesExceptions
{
    use InteractsWithTime;

    /**
     * The Redis factory implementation.
     *
     * @var \WpStarter\Contracts\Redis\Factory
     */
    protected $redis;

    /**
     * The rate limiter instance.
     *
     * @var \WpStarter\Redis\Limiters\DurationLimiter
     */
    protected $limiter;

    /**
     * Process the job.
     *
     * @param  mixed  $job
     * @param  callable  $next
     * @return mixed
     */
    public function handle($job, $next)
    {
        $this->redis = Container::getInstance()->make(Redis::class);

        $this->limiter = new DurationLimiter(
            $this->redis, $this->getKey($job), $this->maxAttempts, $this->decayMinutes * 60
        );

        if ($this->limiter->tooManyAttempts()) {
            return $job->release($this->limiter->decaysAt - $this->currentTime());
        }

        try {
            $next($job);

            $this->limiter->clear();
        } catch (Throwable $throwable) {
            if ($this->whenCallback && ! call_user_func($this->whenCallback, $throwable)) {
                throw $throwable;
            }

            $this->limiter->acquire();

            return $job->release($this->retryAfterMinutes * 60);
        }
    }
}
