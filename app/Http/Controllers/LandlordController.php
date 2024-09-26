<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Landlord;
use App\Models\Tenant;
use App\Rules\UniqueEmail;
use Illuminate\Http\Request;

class LandlordController extends Controller
{

    public function index()
    {
        try {
            $landlords = Landlord::all(); // Retrieve all admins

            return response()->json([
                'landlords' => $landlords
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


            $landlord = Landlord::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_number'=>$request->phone_number,
                'address'=>$request->address,
                'password' => bcrypt($request->password),
            ]);

            return response()->json([
                'message' => 'Landlord created successfully',
                'landlord' => $landlord
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, Landlord $landlord)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => ['required', 'string', 'email', 'max:255', new UniqueEmail([Tenant::class, Landlord::class, Admin::class], $landlord->id)] ,
                'phone_number' => 'required|string|max:15',
                'address' => 'required|string',
            ]);

            $landlord->update($request->all());

            return response()->json([
                'message' => 'Landlord updated successfully',
                'landlord' => $landlord
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
            $landlord = Landlord::findOrFail($id); // Find the landlord by ID or fail if not found
            $landlord->delete(); // Delete the landlord

            return response()->json([
                'message' => 'Landlord deleted successfully'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

}