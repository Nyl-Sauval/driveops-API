<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Google\Client as GoogleClient;


class AuthController extends Controller
{
    // Register new user
    public function register(Request $request)
    {
        $validated = $request->validate([
            'firstName' => 'required|string|max:255',
            'lastName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'firstname' => $validated['firstName'],
            'lastname' => $validated['lastName'],
            'email' => $validated['email'],
            'role' => User::ROLE_USER, // Default role
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('api-token', ['*'])->plainTextToken;

        $accessToken = $user->tokens()->latest()->first();
        $accessToken->expires_at = now()->addHour();
        $accessToken->save();

        return response()->json([
            'user' => $user,
            'expires_in' => 3600,
            'token' => $token,
        ], 201);
    }

    // Login user and create token
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token', ['*'])->plainTextToken;

        $accessToken = $user->tokens()->latest()->first();
        $accessToken->expires_at = now()->addHour();
        $accessToken->save();

        return response()->json([
            'user' => $user,
            'expires_in' => 3600,
            'token' => $token,
        ]);
    }

    // Logout user (revoke current token)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    // Get authenticated user info
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // Log through Google OAuth
    public function googleLogin(Request $request)
    {
        $googleToken = $request->token;

        $client = new GoogleClient([
            'client_id' => env('GOOGLE_CLIENT_ID'),
        ]);
        $payload = $client->verifyIdToken($googleToken);

        if(!$payload) {
            return response()->json(['error' => 'Invalid Google token.'], 401);
        }

        $user = User::updateOrCreate(
            ['google_id' => $payload['sub']],
            [
                'email' => $payload['email'],
                'firstname' => $payload['given_name'] ?? '',
                'lastname' => $payload['family_name'] ?? '',
                'avatar' => $payload['picture'] ?? null,
                'role' => User::ROLE_USER,
                'password' => null,
            ]
        );

        $token = $user->createToken('api-token', ['*'])->plainTextToken;

        $accessToken = $user->tokens()->latest()->first();
        $accessToken->expires_at = now()->addHour();
        $accessToken->save();

        return response()->json([
            'user' => $user,
            'expires_in' => 3600,
            'token' => $token,
        ]);

    }

    // Refresh token
    public function refresh(Request $request)
    {
        $user = $request->user();
        $user->currentAccessToken()->delete();

        $plainTextToken = $user->createToken('api-token')->plainTextToken;

        $accessToken = $user->tokens()->latest()->first();
        $accessToken->expires_at = now()->addHour();
        $accessToken->save();

        return response()->json([
            'token' => $plainTextToken,
            'expires_in' => 3600,
        ]);
    }
}
