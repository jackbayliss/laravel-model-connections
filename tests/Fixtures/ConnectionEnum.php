<?php

namespace JackBayliss\LaravelModelConnection\Tests\Fixtures;

enum ConnectionEnum: string
{
    case Read = 'read';
    case Write = 'write';
}
