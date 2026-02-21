<?php

namespace JackBayliss\LaravelModelConnection\Traits;

trait HasReadWriteConnectionFactory
{
    public function configure(): static
    {
        return $this->withReadWriteConnection();
    }

    public function withReadWriteConnection(): static
    {
        return $this->afterMaking(function ($model) {
            $model->setWriteConnection($model->getReadConnectionName());
        });
    }
}
