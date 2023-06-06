<?php

declare(strict_types=1);

namespace App\Enums;

enum RolesEnum: int
{
    case ADMIN = 1;
    case VENDOR = 2;
    case BUYER = 3;
}
