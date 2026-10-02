<?php

namespace WpStarter\Foundation\Testing\Concerns;

use WpStarter\Foundation\Testing\Wormhole;
use WpStarter\Support\Carbon;

trait InteractsWithTime
{
    /**
     * @template TReturn of mixed
     *
     * Freeze time.
     *
     * @param  (callable(\WpStarter\Support\Carbon): TReturn)|null  $callback
     * @return ($callback is null ? \WpStarter\Support\Carbon : TReturn)
     */
    public function freezeTime($callback = null)
    {
        $result = $this->travelTo($now = Carbon::now(), $callback);

        return is_null($callback) ? $now : $result;
    }

    /**
     * @template TReturn of mixed
     *
     * Freeze time at the beginning of the current second.
     *
     * @param  (callable(\WpStarter\Support\Carbon): TReturn)|null  $callback
     * @return ($callback is null ? \WpStarter\Support\Carbon : TReturn)
     */
    public function freezeSecond($callback = null)
    {
        $result = $this->travelTo($now = Carbon::now()->startOfSecond(), $callback);

        return is_null($callback) ? $now : $result;
    }

    /**
     * Begin travelling to another time.
     *
     * @param  int  $value
     * @return \WpStarter\Foundation\Testing\Wormhole
     */
    public function travel($value)
    {
        return new Wormhole($value);
    }

    /**
     * @template TReturn of mixed
     * @template TDate of \DateTimeInterface|\Closure|\WpStarter\Support\Carbon|string|bool|null
     *
     * Travel to another time.
     *
     * @param  TDate  $date
     * @param  (callable(TDate): TReturn)|null  $callback
     * @return ($callback is null ? void : TReturn)
     */
    public function travelTo($date, $callback = null)
    {
        Carbon::setTestNow($date);

        if ($callback) {
            return ws_tap($callback($date), function () {
                Carbon::setTestNow();
            });
        }
    }

    /**
     * Travel back to the current time.
     *
     * @return \DateTimeInterface
     */
    public function travelBack()
    {
        return Wormhole::back();
    }
}
