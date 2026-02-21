<?php

namespace JackBayliss\LaravelModelConnection\Tests\Fixtures;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JackBayliss\LaravelModelConnection\Traits\HasReadWriteConnection;

class PostModel extends Model
{
    use HasReadWriteConnection;

    protected $table = 'posts';

    protected $guarded = [];

    protected $connection = 'default';

    protected $readConnection = 'read';

    protected $writeConnection = 'write';

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
}
