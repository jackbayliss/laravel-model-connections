<?php

namespace JackBayliss\LaravelModelConnection\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JackBayliss\LaravelModelConnection\Traits\HasReadWriteConnection;

class UserModel extends Model
{
    use HasFactory, HasReadWriteConnection;

    protected static $factory = UserModelFactory::class;

    protected $table = 'users';

    protected $guarded = [];

    protected $connection = 'default';

    protected $readConnection = 'read';

    protected $writeConnection = 'write';

    public function posts(): HasMany
    {
        return $this->hasMany(PostModel::class, 'user_id');
    }
}
