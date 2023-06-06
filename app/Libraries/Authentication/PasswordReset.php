<?php
declare(strict_types=1);

namespace App\Libraries\Authentication;


use App\Contracts\Data\PasswordReset\PasswordResetDataObject;
use App\Contracts\Data\PasswordReset\PasswordResetLinkDataObject;
use App\Models\Auth\PasswordResetTokens;
use Carbon\Carbon;
use Illuminate\Auth\Passwords\PasswordBroker;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Hashing\Hasher as HasherContract;

readonly class PasswordReset
{
    public function __construct(
        private PasswordBroker $broker,
        private HasherContract $hash,
        private Repository     $config
    ) {}
    public function send(PasswordResetLinkDataObject $data): string
    {
        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        return $this->broker->sendResetLink(['email' => $data->email]);
    }

    public function validatePasswordResetToken(PasswordResetDataObject $data, PasswordResetTokens $token = null) : bool
    {
        return $token &&
            ! $this->tokenExpired($token['created_at']) &&
            $this->hash->check($data->token, $token['token']);
    }


    private function tokenExpired(Carbon $createdAt): bool
    {
        return Carbon::parse($createdAt)
            ->addMinutes($this->config->get('auth.passwords.users.expire'))
            ->isPast();
    }
}
