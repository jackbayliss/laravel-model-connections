<?php

namespace JackBayliss\LaravelModelConnection\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\Factory;
use JackBayliss\LaravelModelConnection\Traits\HasReadWriteConnectionFactory;

class UserModelFactory extends Factory
{
    use HasReadWriteConnectionFactory;

    protected $model = UserModel::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
        ];
    }
}
