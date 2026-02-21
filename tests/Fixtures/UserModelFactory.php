<?php

namespace JackBayliss\LaravelModelConnection\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;

class UserModelFactory extends Factory
{
    protected $model = UserModel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
