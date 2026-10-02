<?php

namespace WpStarter\Contracts\Validation;

use WpStarter\Validation\Validator;

interface ValidatorAwareRule
{
    /**
     * Set the current validator.
     *
     * @param  \WpStarter\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator(Validator $validator);
}
