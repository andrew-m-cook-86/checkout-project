<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Contracts\Data\PasswordReset\PasswordResetDataObject;
use App\Contracts\Data\PasswordReset\PasswordResetLinkDataObject;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\PasswordResetLinkRequest;
use App\Http\Requests\Auth\PasswordResetRequest;
use App\Libraries\Authentication\PasswordReset;
use App\Repositories\Users\UserResetPasswordRepository;
use Illuminate\Contracts\Auth\PasswordBroker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function __construct(
        private readonly PasswordReset $passwordReset,
        private readonly UserResetPasswordRepository $userResetPasswordRepository
    )
    {

    }

    /**
     * Display the password reset link request view.
     */
    public function showRequest(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws ValidationException
     */
    public function sendLink(PasswordResetLinkRequest $request, PasswordResetLinkDataObject $dataObject): RedirectResponse
    {
        $sendStatus = $this->passwordReset->send($dataObject::fromRequest($request));

        if($sendStatus === PasswordBroker::RESET_LINK_SENT){
            return back()->with('status', __(PasswordBroker::RESET_LINK_SENT));
        }

        throw ValidationException::withMessages([
            'email' => [trans($sendStatus)],
        ]);
    }

    /**
     * Display the password reset view.
     */
    public function showReset(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    /**
     * Handle an incoming new password request.
     *
     * @throws ValidationException
     */
    public function resetPassword(PasswordResetRequest $request, PasswordResetDataObject $dataObject): RedirectResponse
    {
        $status = $this->userResetPasswordRepository->resetPassword($dataObject::fromRequest($request));

        if ($status === PasswordBroker::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
