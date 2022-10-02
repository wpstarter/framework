<?php

namespace WpStarter\Support\Traits;

trait Tappable
{
    /**
     * Call the given Closure with this instance then return the instance.
     *
     * @param  callable|null  $callback
     * @return $this|\WpStarter\Support\HigherOrderTapProxy
     */
    public function tap($callback = null)
    {
        return ws_tap($this, $callback);
    }
}
