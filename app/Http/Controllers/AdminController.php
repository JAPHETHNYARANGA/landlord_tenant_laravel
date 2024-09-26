<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Rules\UniqueEmail;
use Illuminate\Http\Request;

class AdminController extends Controller
{

    public function index()
    {
        try {
            $admins = Admin::all(); // Retrieve all admins

            return response()->json([
                'admins' => $admins
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', new UniqueEmail([Tenant::class, Landlord::class, Admin::class])],
                'password' => 'required|string|min:8',
            ]);

            $admin = Admin::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'message' => 'Admin created successfully',
                'admin' => $admin
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Admin $admin)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', new UniqueEmail([Tenant::class, Landlord::class, Admin::class], $admin->id)],
                'password' => 'nullable|string|min:8',
            ]);

            $admin->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password ? bcrypt($request->password) : $admin->password,
            ]);

            return response()->json([
                'message' => 'Admin updated successfully',
                'admin' => $admin
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $admin = Admin::findOrFail($id); // Find the landlord by ID or fail if not found
            $admin->delete(); // Delete the landlord

            return response()->json([
                'message' => 'Admin deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}