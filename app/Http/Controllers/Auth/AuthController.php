<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:6'
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password']),
            'verification_code' => $this->generateVerificationCode()
        ]);

        $this->logVerificationCode($user);

        return $this->respondWithToken($user);
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'phone' => 'required|string',
            'password' => 'required|string'
        ]);

        if (!Auth::attempt(['phone' => $credentials['phone'], 'password' => $credentials['password']])) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $user = Auth::user();

        if (!$user->is_verified) {
            return response()->json(['message' => 'Account not verified'], 403);
        }

        return $this->respondWithToken($user);
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'phone' => 'required|string',
            'code' => 'required|string|size:6'
        ]);

        $user = User::where('phone', $validated['phone'])
            ->where('verification_code', $validated['code'])
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid verification code'], 400);
        }

        $this->markUserAsVerified($user);

        return response()->json(['message' => 'Account verified successfully']);
    }

    private function generateVerificationCode(): string
    {
        return sprintf("%06d", random_int(0, 999999));
    }

    private function logVerificationCode(User $user): void
    {
        Log::info("Verification code for user {$user->phone}: {$user->verification_code}");
    }

    private function markUserAsVerified(User $user): void
    {
        $user->update([
            'is_verified' => true,
            'verification_code' => null
        ]);
    }

    private function respondWithToken(User $user): \Illuminate\Http\JsonResponse
    {
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ]);
    }
}
