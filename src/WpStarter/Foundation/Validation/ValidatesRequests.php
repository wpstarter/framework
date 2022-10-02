<?php

namespace WpStarter\Foundation\Validation;

use WpStarter\Contracts\Validation\Factory;
use WpStarter\Http\Request;
use WpStarter\Validation\ValidationException;

trait ValidatesRequests
{
    /**
     * Run the validation routine against the given validator.
     *
     * @param  \WpStarter\Contracts\Validation\Validator|array  $validator
     * @param  \WpStarter\Http\Request|null  $request
     * @return array
     *
     * @throws \WpStarter\Validation\ValidationException
     */
    public function validateWith($validator, Request $request = null)
    {
        $request = $request ?: ws_request();

        if (is_array($validator)) {
            $validator = $this->getValidationFactory()->make($request->all(), $validator);
        }

        return $validator->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  \WpStarter\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \WpStarter\Validation\ValidationException
     */
    public function validate(Request $request, array $rules,
                             array $messages = [], array $customAttributes = [])
    {
        return $this->getValidationFactory()->make(
            $request->all(), $rules, $messages, $customAttributes
        )->validate();
    }

    /**
     * Validate the given request with the given rules.
     *
     * @param  string  $errorBag
     * @param  \WpStarter\Http\Request  $request
     * @param  array  $rules
     * @param  array  $messages
     * @param  array  $customAttributes
     * @return array
     *
     * @throws \WpStarter\Validation\ValidationException
     */
    public function validateWithBag($errorBag, Request $request, array $rules,
                                    array $messages = [], array $customAttributes = [])
    {
        try {
            return $this->validate($request, $rules, $messages, $customAttributes);
        } catch (ValidationException $e) {
            $e->errorBag = $errorBag;

            throw $e;
        }
    }

    /**
     * Get a validation factory instance.
     *
     * @return \WpStarter\Contracts\Validation\Factory
     */
    protected function getValidationFactory()
    {
        return ws_app(Factory::class);
    }
}
