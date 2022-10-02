<?php

namespace WpStarter\Auth\Access\Events;

class GateEvaluated
{
    /**
     * The authenticatable model.
     *
     * @var \WpStarter\Wordpress\User|null
     */
    public $user;

    /**
     * The ability being evaluated.
     *
     * @var string
     */
    public $ability;

    /**
     * The result of the evaluation.
     *
     * @var bool|null
     */
    public $result;

    /**
     * The arguments given during evaluation.
     *
     * @var array
     */
    public $arguments;

    /**
     * Create a new event instance.
     *
     * @param  \WpStarter\Wordpress\User|null  $user
     * @param  string  $ability
     * @param  bool|null  $result
     * @param  array  $arguments
     * @return void
     */
    public function __construct($user, $ability, $result, $arguments)
    {
        $this->user = $user;
        $this->ability = $ability;
        $this->result = $result;
        $this->arguments = $arguments;
    }
}
