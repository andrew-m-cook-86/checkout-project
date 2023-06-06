<?php

declare(strict_types=1);

namespace App\Repositories\Users;

use App\Contracts\Data\PasswordReset\PasswordResetDataObject;
use App\Contracts\Data\User\PasswordUpdateDataObject;
use App\Contracts\Data\User\ProfileUpdateDataObject;
use App\Contracts\Data\User\UserCreateDataObject;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

readonly class UserDbRepository
{
    public function __construct(private User $userModel){}

    /**
     * @param UserCreateDataObject $data
     * @return User
     */
    public function register(UserCreateDataObject $data): User
    {
        return $this->userModel->create(
            [
                'name' => $data->name,
                'email' => $data->email,
                'password' => $data->password
            ]
        );
    }

    /**
     * @param User $user
     * @return User
     */
    public function verifyEmail(User $user): User
    {
        $user->forceFill([
            'email_verified_at' => Carbon::now(),
        ])->save();

        return $user;
    }

    /**
     * @param User $user
     * @param PasswordUpdateDataObject $data
     * @return void
     */
    public function updatePassword(User $user, PasswordUpdateDataObject $data): void
    {
        $user->update([
            'password' => $data->password,
        ]);
    }

    /**
     * @param User $user
     * @param ProfileUpdateDataObject $data
     * @return void
     */
    public function updateProfile(User $user, ProfileUpdateDataObject $data): void
    {
        $user->update([
            'name' => $data->name,
        ]);
    }

    /**
     * @param User $user
     * @return bool|null
     */
    public function deleteProfile(User $user): bool|null
    {
        return $user->delete();
    }

    /**
     * @param string $email
     * @return User|null
     */
    public function getUserByEmail(string $email): User|null
    {
        return $this->userModel->where('email', $email)->first();
    }

    /**
     * @param User $user
     * @param PasswordResetDataObject $data
     * @return void
     */
    public function resetPassword(User $user, PasswordResetDataObject $data): void
    {
        $user->forceFill([
            'password' => $data->hashedPassword,
            'remember_token' => Str::random(60),
        ])->save();
    }
}
