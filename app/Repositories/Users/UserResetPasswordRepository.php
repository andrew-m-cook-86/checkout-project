<?php
declare(strict_types=1);

namespace App\Repositories\Users;

use App\Contracts\Data\PasswordReset\PasswordResetDataObject;
use App\Libraries\Authentication\PasswordReset;
use App\Repositories\Auth\AuthDbRepository;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Contracts\Events\Dispatcher;

readonly class UserResetPasswordRepository
{
    /**
     * @param UserDbRepository $userDbRepository
     * @param AuthDbRepository $authDbRepository
     * @param PasswordReset $passwordReset
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        private UserDbRepository $userDbRepository,
        private AuthDbRepository $authDbRepository,
        private PasswordReset $passwordReset,
        private Dispatcher $dispatcher,
    ) {}

    /**
     * @param PasswordResetDataObject $data
     * @return string
     */
    public function resetPassword(PasswordResetDataObject $data): string
    {
        $user = $this->userDbRepository->getUserByEmail($data->email);
        if(!$user) {
            return PasswordBroker::INVALID_USER;
        }

        $token = $this->authDbRepository->checkPasswordResetTokenExists($data->email);
        if(!$this->passwordReset->validatePasswordResetToken($data, $token)) {
            return PasswordBroker::INVALID_TOKEN;
        }

        $this->userDbRepository->resetPassword($user, $data);
        $this->authDbRepository->deleteTokenViaEmail($data->email);
        $this->dispatcher->dispatch(new \Illuminate\Auth\Events\PasswordReset($user));

        return PasswordBroker::PASSWORD_RESET;
    }
}
