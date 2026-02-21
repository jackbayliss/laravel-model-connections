<?php

namespace JackBayliss\LaravelModelConnection\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use JackBayliss\LaravelModelConnection\Traits\HasReadWriteConnection;

class DefaultModel extends Model
{
    use HasReadWriteConnection;

    protected $connection = 'sqlite';
    // readConnection and writeConnection intentionally left unset
}
