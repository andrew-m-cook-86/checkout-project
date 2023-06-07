<?php
declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumHelpers;

enum InstructionStatusEnum: string
{
    use EnumHelpers;

    case COMPLETED = 'COMPLETED';
    case FAILED = 'FAILED';
}
