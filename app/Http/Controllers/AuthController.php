<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Auth::login($user);

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => [
                    'user' => $user,
                    'message' => 'Registration successful'
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to register',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $credentials = $request->only('email', 'password');

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user' => Auth::user(),
                        'message' => 'Login successful'
                    ]
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to login',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // If it's an AJAX request, return JSON with redirect info
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Logout successful',
                    'redirect' => '/login'
                ]);
            }

            // Otherwise, redirect directly
            return redirect('/login');
        } catch (\Exception $e) {
            // If it's an AJAX request, return JSON error
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to logout',
                    'error' => $e->getMessage()
                ], 500);
            }

            // Otherwise, redirect to login with error
            return redirect('/login')->with('error', 'Failed to logout');
        }
    }

    public function profile()
    {
        try {
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Profile retrieved successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            $user = Auth::user();

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                'password' => ['nullable', 'confirmed', Password::defaults()],
            ]);

            DB::beginTransaction();

            $user->name = $request->name;
            $user->email = $request->email;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'Profile updated successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function deleteAccount(Request $request)
    {
        try {
            $user = Auth::user();

            // Verify password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid password'
                ], 422);
            }

            DB::beginTransaction();

            // Delete user (cascade will handle related records)
            $user->delete();

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Account deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete account',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}



