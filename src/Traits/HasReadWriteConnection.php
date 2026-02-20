<?php

namespace JackBayliss\LaravelModelConnection;

use UnitEnum;

use function Illuminate\Support\enum_value;

trait HasReadWriteConnection
{
    /**
     * The read connection name for the model.
     */
    protected UnitEnum|string|null $readConnection;

    /**
     * The write connection name for the model.
     */
    protected UnitEnum|string|null $writeConnection = null;

    /**
     * Get the read database connection for the model.
     */
    public function getReadConnection(): \Illuminate\Database\Connection
    {
        return static::resolveConnection($this->getReadConnectionName());
    }

    /**
     * Get the write database connection for the model.
     */
    public function getWriteConnection(): \Illuminate\Database\Connection
    {
        return static::resolveConnection($this->getWriteConnectionName());
    }

    /**
     * Get the read connection name for the model.
     */
    public function getReadConnectionName(): string|null
    {
        return enum_value($this->readConnection) ?? $this->getConnectionName();
    }

    /**
     * Get the write connection name for the model.
     */
    public function getWriteConnectionName(): string|null
    {
        return enum_value($this->writeConnection) ?? $this->getConnectionName();
    }

    /**
     * Get a new query builder instance for the connection.
     */
    protected function newBaseQueryBuilder(): \Illuminate\Database\Query\Builder
    {
        $connection = clone $this->getWriteConnection();

        $connection->setReadPdo($this->getReadConnection()->getReadPdo());

        return $connection->query();
    }
}
