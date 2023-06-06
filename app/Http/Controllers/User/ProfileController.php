<?php

declare(strict_types=1);

namespace App\Http\Controllers\User;

use App\Contracts\Data\User\PasswordUpdateDataObject;
use App\Contracts\Data\User\ProfileUpdateDataObject;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\PasswordUpdateRequest;
use App\Http\Requests\User\ProfileDeleteRequest;
use App\Http\Requests\User\ProfileUpdateRequest;
use App\Libraries\Authentication\SessionHelper;
use App\Repositories\Users\UserProfileRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(
        private readonly UserProfileRepository $userProfileRepository,
        private readonly SessionHelper $sessionHelper)
    {
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request, ProfileUpdateDataObject $dataObject): RedirectResponse
    {
        $this->userProfileRepository->updateProfile(
            $request->user(),
            $dataObject::fromRequest($request)
        );

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(PasswordUpdateRequest $request, PasswordUpdateDataObject $dataObject): RedirectResponse
    {
        $this->userProfileRepository->updatePassword(
            $request->user(),
            $dataObject::fromRequest($request)
        );

        return Redirect::route('profile.edit')->with('status', 'password-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(ProfileDeleteRequest $request): RedirectResponse
    {
        $status = $this->userProfileRepository->deleteProfile(
            $request->user(),
        );

        if($status) {
            $this->sessionHelper->destroy($request);
            return redirect('/');
        }

        return back();
    }
}
