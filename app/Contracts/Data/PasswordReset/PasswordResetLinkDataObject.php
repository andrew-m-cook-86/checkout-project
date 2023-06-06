<?php

declare(strict_types=1);

namespace App\Contracts\Data\PasswordReset;

use App\Contracts\Interfaces\DataObject;
use Illuminate\Http\Request;

readonly class PasswordResetLinkDataObject implements DataObject
{
    /**
     * @param string $email
     */
    public function __construct(
        public string $email = ''
    ) {
    }

    /**
     * @param Request $request
     * @return self
     */
    public static function fromRequest(Request $request): static
    {
        return new self(
            $request->get('email')
        );
    }
}
