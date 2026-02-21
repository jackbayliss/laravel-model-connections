<?php

namespace JackBayliss\LaravelModelConnection\Traits;

use Illuminate\Database\Connection;
use Illuminate\Database\Query\Builder;
use UnitEnum;

use function Illuminate\Support\enum_value;

trait HasReadWriteConnection
{
    /**
     * Get the read database connection for the model.
     */
    public function getReadConnection(): Connection
    {
        return static::resolveConnection($this->getReadConnectionName());
    }

    /**
     * Get the write database connection for the model.
     */
    public function getWriteConnection(): Connection
    {
        return static::resolveConnection($this->getWriteConnectionName());
    }

    /**
     * Get the read connection name for the model.
     */
    public function getReadConnectionName(): ?string
    {
        return isset($this->readConnection) ? enum_value($this->readConnection) : $this->getConnectionName();
    }

    /**
     * Get the write connection name for the model.
     */
    public function getWriteConnectionName(): ?string
    {
        return isset($this->writeConnection) ? enum_value($this->writeConnection) : $this->getConnectionName();
    }

    /**
     * Set the read connection name for the model.
     */
    public function setReadConnection(UnitEnum|string|null $readConnection): static
    {
        $this->readConnection = $readConnection;

        return $this;
    }

    /**
     * Set the write connection name for the model.
     */
    public function setWriteConnection(UnitEnum|string|null $writeConnection): static
    {
        $this->writeConnection = $writeConnection;

        return $this;
    }

    /**
     * Get a new query builder instance for the connection.
     */
    protected function newBaseQueryBuilder(): Builder
    {
        // We clone the write connection, so we can swap in the read PDO without
        // mutating the shared connection instance held by the connection resolver.
        $connection = clone $this->getWriteConnection();

        $connection->setReadPdo($this->getReadConnection()->getReadPdo());

        return $connection->query();
    }
}
