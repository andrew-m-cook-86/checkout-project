<?php
declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumHelpers;

enum PayoutStatusEnum: string
{
    use EnumHelpers;

    case PENDING = 'PENDING';
    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
    case PARTIAL = 'PARTIAL';
}
