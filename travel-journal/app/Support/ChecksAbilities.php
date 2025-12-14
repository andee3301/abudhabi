<?php

namespace App\Support;

use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

trait ChecksAbilities
{
    protected function ensureAbility(Request $request, string $ability): void
    {
        $token = $request->bearerToken();
        $tokenModel = $token ? PersonalAccessToken::findToken($token) : null;

        abort_unless($tokenModel?->can($ability), 403);
    }
}
