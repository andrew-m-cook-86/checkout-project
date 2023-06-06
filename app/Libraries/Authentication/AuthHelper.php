<?php

declare(strict_types=1);

namespace App\Libraries\Authentication;

use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Auth\AuthManager;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

readonly abstract class AuthHelper
{
    public function __construct(
        protected RateLimiter  $limiter,
        protected AuthManager $authManager,
        protected Dispatcher $dispatcher
    )
    {
    }

    /**
     * @throws ValidationException
     */
    public function create(LoginRequest $request): void
    {
        $this->ensureIsNotRateLimited($request);
        if (! $this->authManager->attempt($request->only('email', 'password'))) {
            $this->limiter->hit($this->throttleKey($request));

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        $this->limiter->clear($this->throttleKey($request));
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(LoginRequest $request): void
    {
        if (! $this->limiter->tooManyAttempts($this->throttleKey($request), 5)) {
            return;
        }

        $this->dispatcher->dispatch(new Lockout($request));

        $seconds = $this->limiter->availableIn($this->throttleKey($request));

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(LoginRequest $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')).'|'.$request->ip());
    }


    public function regenerate(LoginRequest $request): void
    {
        $request->session()->regenerate();
    }
}
