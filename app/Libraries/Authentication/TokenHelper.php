<?php

declare(strict_types=1);

namespace App\Libraries\Authentication;

use Illuminate\Http\Request;

readonly class TokenHelper extends AuthHelper
{
    public function generateToken(): array
    {
        $this->destroy();
        return [
            'token' => $this->authManager->user()->createToken('api')->plainTextToken
        ];
    }

    /**
     * Used for Logging Out
     * @param Request|null $request
     * @return void
     */
    public function destroy(Request $request = null): void
    {
        (is_null($request)) ?
            $this->authManager->user()->tokens()->delete() :
            $request->user()->tokens()->delete();
    }
}
