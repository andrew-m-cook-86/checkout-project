<?php

declare(strict_types=1);

namespace App\Contracts\Interfaces;

use Illuminate\Http\Request;

interface DataObject
{
    public static function fromRequest(Request $request): static;
}
