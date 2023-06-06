<?php

declare(strict_types=1);

namespace App\Contracts\Data\User;

use App\Contracts\Interfaces\DataObject;
use Illuminate\Http\Request;

readonly class ProfileUpdateDataObject implements DataObject
{
    /**
     * @param string $name
     */
    public function __construct(
        public string $name = ""
    ) {
    }

    /**
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): static
    {
        return new self(
            $request->input('name')
        );
    }
}
