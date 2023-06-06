<?php

declare(strict_types=1);

namespace App\Libraries\Authentication;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

readonly class SessionHelper extends AuthHelper
{
    /**
     * Used for Logging Out
     * @param Request $request
     * @return void
     */
    public function destroy(Request $request): void
    {
        $this->authManager->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
