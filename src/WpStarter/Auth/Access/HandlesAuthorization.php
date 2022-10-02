<?php

namespace WpStarter\Auth\Access;

trait HandlesAuthorization
{
    /**
     * Create a new access response.
     *
     * @param  string|null  $message
     * @param  mixed  $code
     * @return \WpStarter\Auth\Access\Response
     */
    protected function allow($message = null, $code = null)
    {
        return Response::allow($message, $code);
    }

    /**
     * Throws an unauthorized exception.
     *
     * @param  string|null  $message
     * @param  mixed|null  $code
     * @return \WpStarter\Auth\Access\Response
     */
    protected function deny($message = null, $code = null)
    {
        return Response::deny($message, $code);
    }
}
