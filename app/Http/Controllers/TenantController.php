<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Rules\UniqueEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
            ]);

            // Create admin without a password
            $tenant  = Tenant::create([
                'name' => $request->name,
                'email' => $request->email,
                'address' => $request->address,
                'phone_number' => $request->phone_number,
                'property_id' =>$request->property_id,
                'house_no' =>$request->house_no
            ]);

            // Generate a password creation token
            $token = Str::random(60);

            // Save token in a custom table
            DB::table('password_creates')->updateOrInsert(
                ['token' => $token],
                [
                    'email' => $tenant->email,
                    'user_type' => 'tenant' 
                ]
            );

            // Create the link to set the password
            $link = route('password.create', ['token' => $token]);

            // Send password creation link to the admin's email
            Mail::send('password_set_link', ['link' => $link], function ($m) use ($tenant ) {
                $m->from('info@landlordtenant.com', 'LandlordTenant');
                $m->to($tenant ->email, $tenant ->name)->subject('Set Password');
            });

            return response()->json([
                'message' => 'Tenant created successfully. A password creation link has been sent to their email.',
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