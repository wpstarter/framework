<?php

namespace WpStarter\Contracts\Validation;

interface ValidatorAwareRule
{
    /**
     * Set the current validator.
     *
     * @param  \WpStarter\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator($validator);
}
