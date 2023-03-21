<?php

namespace WpStarter\Tests\Database\Fixtures\Factories\Money;

use WpStarter\Database\Eloquent\Factories\Factory;

class PriceFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->name,
        ];
    }
}
