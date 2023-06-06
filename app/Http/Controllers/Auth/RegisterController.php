<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Contracts\Data\User\UserCreateDataObject;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserCreateRequest;
use App\Providers\RouteServiceProvider;
use App\Repositories\Users\UserRegisterRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function __construct(private readonly UserRegisterRepository $userRegisterRepository){}

    /**
     * Display the registration view.
     */
    public function show(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request
     */
    public function store(UserCreateRequest $request, UserCreateDataObject $dataObject): RedirectResponse
    {
        $this->userRegisterRepository->register(
            $dataObject::fromRequest($request)
        );

        return redirect(RouteServiceProvider::HOME);
    }
}
