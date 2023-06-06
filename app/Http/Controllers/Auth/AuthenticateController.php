<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Libraries\Authentication\TokenHelper;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Libraries\Authentication\SessionHelper;

class AuthenticateController extends Controller
{
    public function __construct(private readonly SessionHelper $sessionHelper, private readonly TokenHelper $tokenHelper){}

    /**
     * Display the login view.
     */
    public function show(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     * @throws ValidationException
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $this->sessionHelper->create($request);
        $this->sessionHelper->regenerate($request);

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Handle an incoming authentication api request.
     * @throws ValidationException
     */
    public function createToken(LoginRequest $request): JsonResponse
    {
        $this->tokenHelper->create($request);
        $token = $this->tokenHelper->generateToken();

        return response()->json($token);
    }

    /**
     * Destroy a token.
     */
    public function destroyToken(Request $request): JsonResponse
    {
        $this->tokenHelper->destroy($request);

        return response()->json();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->sessionHelper->destroy($request);

        return redirect('/');
    }
}
