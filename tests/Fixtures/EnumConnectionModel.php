<?php

namespace JackBayliss\LaravelModelConnection\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use JackBayliss\LaravelModelConnection\Traits\HasReadWriteConnection;

class EnumConnectionModel extends Model
{
    use HasReadWriteConnection;

    protected $table = 'users';

    protected $guarded = [];

    protected $connection = 'default';

    protected $readConnection = ConnectionEnum::Read;

    protected $writeConnection = ConnectionEnum::Write;
}
