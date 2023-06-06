<?php
declare(strict_types=1);

namespace App\Repositories\Users;

use App\Contracts\Data\User\UserCreateDataObject;
use App\Events\UserSignUpEvent;
use App\Models\User;
use Illuminate\Auth\AuthManager;
use Illuminate\Contracts\Events\Dispatcher;

readonly class UserRegisterRepository
{
    public function __construct(
        private UserDbRepository $userDbRepository,
        private Dispatcher $dispatcher,
        private AuthManager $authManager
    ) {}

    public function register(UserCreateDataObject $data): void
    {
        $user = $this->userDbRepository->register($data);
        $this->dispatcher->dispatch(new UserSignUpEvent($user));
        $this->authManager->login($user);
    }

    public function verifyEmail(User $user): void
    {
        $this->userDbRepository->verifyEmail($user);
    }
}
