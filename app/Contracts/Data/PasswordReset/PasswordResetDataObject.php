<?php

declare(strict_types=1);

namespace App\Contracts\Data\PasswordReset;

use App\Contracts\Interfaces\DataObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

readonly class PasswordResetDataObject implements DataObject
{
    /**
     * @param string $email
     * @param string $password
     * @param string $passwordConfirmation
     * @param string $token
     * @param string $hashedPassword
     */
    public function __construct(
        public string $email = "",
        public string $password = "",
        public string $passwordConfirmation = "",
        public string $token = "",
        public string $hashedPassword = ""
    ) {
    }

    /**
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): static
    {
        return new self(
            $request->get('email'),
            $request->get('password'),
            $request->get('password_confirmation'),
            $request->get('token'),
            Hash::make($request->get('password'))
        );
    }
}
