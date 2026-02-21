<?php

namespace JackBayliss\LaravelModelConnection\Tests;

use Illuminate\Database\Schema\Blueprint;
use JackBayliss\LaravelModelConnection\Tests\Fixtures\DefaultModel;
use JackBayliss\LaravelModelConnection\Tests\Fixtures\EnumConnectionModel;
use JackBayliss\LaravelModelConnection\Tests\Fixtures\PostModel;
use JackBayliss\LaravelModelConnection\Tests\Fixtures\UserModel;
use Orchestra\Testbench\TestCase;

class HasReadWriteConnectionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        foreach (['read', 'write', 'default'] as $connection) {
            $this->app['db']->connection($connection)
                ->getSchemaBuilder()
                ->create('users', function (Blueprint $table) {
                    $table->id();
                    $table->string('name');
                    $table->timestamps();
                });

            $this->app['db']->connection($connection)
                ->getSchemaBuilder()
                ->create('posts', function (Blueprint $table) {
                    $table->id();
                    $table->foreignId('user_id');
                    $table->string('title');
                    $table->timestamps();
                });
        }

        $now = now();

        $this->app['db']->connection('read')->table('users')->insert(['id' => 1, 'name' => 'read-only-user', 'created_at' => $now, 'updated_at' => $now]);
        $this->app['db']->connection('read')->table('posts')->insert(['id' => 1, 'user_id' => 1, 'title' => 'read-only-post', 'created_at' => $now, 'updated_at' => $now]);

        $this->app['db']->connection('write')->table('users')->insert(['id' => 2, 'name' => 'write-only-user', 'created_at' => $now, 'updated_at' => $now]);
        $this->app['db']->connection('write')->table('posts')->insert(['id' => 2, 'user_id' => 2, 'title' => 'write-only-post', 'created_at' => $now, 'updated_at' => $now]);

        $this->app['db']->connection('default')->table('users')->insert(['id' => 3, 'name' => 'default-user', 'created_at' => $now, 'updated_at' => $now]);
        $this->app['db']->connection('default')->table('posts')->insert(['id' => 3, 'user_id' => 3, 'title' => 'default-post', 'created_at' => $now, 'updated_at' => $now]);
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.connections.read', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $app['config']->set('database.connections.write', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);

        $app['config']->set('database.connections.default', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
    }

    protected function seedConnection(string $connection, int $id, string $name): void
    {
        $now = now();

        $this->app['db']->connection($connection)->table('users')->insert(['id' => $id, 'name' => $name, 'created_at' => $now, 'updated_at' => $now]);
    }

    public function test_it_falls_back_to_the_model_connection_when_no_read_or_write_connection_is_set(): void
    {
        $model = new DefaultModel;

        $this->assertSame('sqlite', $model->getReadConnectionName());
        $this->assertSame('sqlite', $model->getWriteConnectionName());
    }

    public function test_get_read_connection_returns_the_read_connection(): void
    {
        $model = new UserModel;

        $this->assertSame('read', $model->getReadConnection()->getName());
    }

    public function test_get_write_connection_returns_the_write_connection(): void
    {
        $model = new UserModel;

        $this->assertSame('write', $model->getWriteConnection()->getName());
    }

    public function test_set_read_connection_overrides_the_read_connection(): void
    {
        $model = (new UserModel)->setReadConnection('default');

        $this->assertSame('default', $model->getReadConnectionName());
    }

    public function test_set_write_connection_overrides_the_write_connection(): void
    {
        $model = (new UserModel)->setWriteConnection('default');

        $this->assertSame('default', $model->getWriteConnectionName());
    }

    public function test_it_reads_from_the_read_connection_when_set_via_enum(): void
    {
        $user = EnumConnectionModel::first();

        $this->assertSame('read-only-user', $user->name);
    }

    public function test_it_reads_from_the_read_connection(): void
    {
        $user = UserModel::first();

        $this->assertSame('read-only-user', $user->name);
        $this->assertDatabaseMissing('users', ['name' => 'read-only-user'], 'write');
    }

    public function test_it_writes_to_the_write_connection(): void
    {
        UserModel::create(['name' => 'new-user']);

        $this->assertDatabaseHas('users', ['name' => 'new-user'], 'write');
        $this->assertDatabaseMissing('users', ['name' => 'new-user'], 'read');
    }

    public function test_updates_go_to_the_write_connection(): void
    {
        $this->seedConnection('write', 10, 'original');
        $this->seedConnection('read', 10, 'original');

        UserModel::where('id', 10)->update(['name' => 'updated']);

        $this->assertDatabaseHas('users', ['id' => 10, 'name' => 'updated'], 'write');
        $this->assertDatabaseHas('users', ['id' => 10, 'name' => 'original'], 'read');
    }

    public function test_deletes_go_to_the_write_connection(): void
    {
        $this->seedConnection('write', 10, 'delete-me');
        $this->seedConnection('read', 10, 'delete-me');

        UserModel::destroy(10);

        $this->assertDatabaseMissing('users', ['id' => 10], 'write');
        $this->assertDatabaseHas('users', ['id' => 10, 'name' => 'delete-me'], 'read');
    }

    public function test_has_many_relationship_queries_the_read_connection(): void
    {
        $user = UserModel::first();

        $this->assertSame('read-only-post', $user->posts->first()->title);
    }

    public function test_belongs_to_relationship_queries_the_read_connection(): void
    {
        $post = PostModel::first();

        $this->assertSame('read-only-user', $post->user->name);
    }

    public function test_eager_loaded_relationship_queries_the_read_connection(): void
    {
        $user = UserModel::with('posts')->first();

        $this->assertSame('read-only-post', $user->posts->first()->title);
    }
}
