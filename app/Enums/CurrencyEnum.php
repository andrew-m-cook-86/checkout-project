<?php

declare(strict_types=1);

namespace App\Enums;

use App\Traits\EnumHelpers;

enum CurrencyEnum: int
{
    use EnumHelpers;

    case USD = 1;
    case EUR = 2;
    case GBP = 3;
}
