<?php
declare(strict_types=1);

namespace App\Repositories\Users;

use App\Contracts\Data\User\PasswordUpdateDataObject;
use App\Contracts\Data\User\ProfileUpdateDataObject;
use App\Models\User;

readonly class UserProfileRepository
{
    public function __construct(
        private UserDbRepository $userDbRepository
    ) {}

    public function updatePassword(User $user, PasswordUpdateDataObject $data): void
    {
        $this->userDbRepository->updatePassword($user, $data);
    }

    public function updateProfile(User $user, ProfileUpdateDataObject $data): void
    {
        $this->userDbRepository->updateProfile($user, $data);
    }

    public function deleteProfile(User $user): bool
    {
        if($this->userDbRepository->deleteProfile($user)){
            return true;
        }

        return false;
    }
}
