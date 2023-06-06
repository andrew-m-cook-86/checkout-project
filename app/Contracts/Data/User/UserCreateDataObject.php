<?php

declare(strict_types=1);

namespace App\Contracts\Data\User;

use App\Contracts\Interfaces\DataObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

readonly class UserCreateDataObject implements DataObject
{
    /**
     * @param string $name
     * @param string $email
     * @param string $password
     */
    public function __construct(
        public string $name = '',
        public string $email = '',
        public string $password = '',
    ) {
    }

    /**
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): static
    {
        return new self(
            $request->input('name'),
            $request->input('email'),
            Hash::make($request->input('password'))
        );
    }
}
