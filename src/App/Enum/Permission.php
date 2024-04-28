<?php

namespace App\Enum;

enum Permission: string
{
    case READ = 'read';
    case WRITE = 'write';
}
