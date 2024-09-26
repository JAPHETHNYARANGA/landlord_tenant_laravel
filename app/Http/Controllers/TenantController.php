<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Rules\UniqueEmail;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        try {
            $tenants = Tenant::all(); // Retrieve all admins

            return response()->json([
                'tenants' => $tenants
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
                'phone_number' => 'required|string|max:15',
                'address' => 'required|string',
                'password' => 'nullable|string|min:8',
            ]);

            $tenant = Tenant::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number'=>$request->phone_number,
                'address'=>$request->address,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'message' => 'Tenant created successfully',
                'tenant' => $tenant
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Tenant $tenant)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', new UniqueEmail([Tenant::class, Landlord::class, Admin::class], $tenant->id)],
                'phone_number' => 'required|string|max:15',
                'address' => 'required|string',
            ]);

            $tenant->update($request->all());

            return response()->json([
                'message' => 'Tenant updated successfully',
                'tenant' => $tenant
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
            $tenant = Tenant::findOrFail($id); // Find the landlord by ID or fail if not found
            $tenant->delete(); // Delete the landlord

            return response()->json([
                'message' => 'Tenant deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }
}