<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Landlord;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            // Validate the request data
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // Find the user by email
            $user = $this->findUserByEmail($request->email);

            if ($user && Hash::check($request->password, $user->password)) {
                // Create a new token for the user if using token-based auth
                $token = $user->createToken('authentication')->plainTextToken;

                return response()->json([
                    'success' => true,
                    'user_type' => $user->user_type, // Return the user type
                    'user' => $user,
                    'token' => $token, // Include token if using token-based auth
                ]);
            }

            return response()->json(['error' => 'Invalid credentials'], 401);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Find a user by their email address.
     *
     * @param  string  $email
     * @return mixed
     */
    private function findUserByEmail($email)
    {
        try {
            $user = null;
            $userType = null;

            // Define user types in an array for iteration
            $userTypes = [
                'landlord' => Landlord::class,
                'tenant' => Tenant::class,
                'admin' => Admin::class,
            ];

            foreach ($userTypes as $type => $model) {
                $user = $model::where('email', $email)->first();
                if ($user) {
                    $userType = $type;
                    break;
                }
            }

            if ($user) {
                $user->user_type = $userType; // Set user type to found type
                return $user;
            }

            return null;

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

}
