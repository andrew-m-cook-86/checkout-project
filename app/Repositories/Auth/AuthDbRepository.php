<?php
declare(strict_types=1);

namespace App\Repositories\Auth;

use App\Models\Auth\PasswordResetTokens;

class AuthDbRepository
{
    public function __construct(private PasswordResetTokens $passwordResetTokens){}

    public function checkPasswordResetTokenExists(string $email): PasswordResetTokens
    {
        return $this->passwordResetTokens->where('email', $email)->first();
    }

    public function deleteTokenViaEmail(string $email): void
    {
        $this->passwordResetTokens->where('email', $email)->delete();
    }
}
