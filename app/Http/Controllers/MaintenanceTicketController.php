<?php

namespace App\Http\Controllers;

use App\Models\Landlord;
use App\Models\MaintenanceTicket;
use App\Models\Properties;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class MaintenanceTicketController extends Controller
{
     // Display a listing of maintenance tickets
     public function index()
     {
         $tickets = MaintenanceTicket::all();
         return response()->json($tickets);
     }
 
     // Store a newly created maintenance ticket
     public function store(Request $request)
     {
        try {
            $request->validate([
                'tenant_id' => 'required|exists:tenants,id',
                'property_id' => 'required|exists:properties,id',
                'issue' => 'required|string|max:255',
                'description' => 'required|string',
            ]);

            // Create a new maintenance ticket
            $ticket = MaintenanceTicket::create($request->all());

            $property = Properties::where('id',$ticket->property_id )->first();

            $property_landlord = $property->landlord_id;

            // Find the landlord associated with the property
            $landlord = Landlord::where('id', $property_landlord)->first();

            if ($landlord) {
                // Notify the landlord via email
                Mail::send('ticket_notification', [
                    'ticket' => $ticket,
                    'landlord' => $landlord,
                ], function ($message) use ($landlord) {
                    $message->from('info@landlordtenant.com', 'LandlordTenant');
                    $message->to($landlord->email)->subject('New Maintenance Ticket Created');
                });
            }

            return response()->json([
                'message' => 'Maintenance ticket created successfully.',
                'ticket' => $ticket,
            ], 201);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
     }
 
     // Display the specified maintenance ticket
     public function show($id)
     {
        try{
            $ticket = MaintenanceTicket::findOrFail($id);
            return response()->json($ticket);

        }catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
        
     }
 
      // Update the specified maintenance ticket's status
    public function update(Request $request, $id)
    {
        

        try{

            $request->validate([
                'status' => 'required|in:closed',
            ]);
    
            // Check if the user is authorized (landlord or admin)
    
            // $user = Auth::user();
            // if (!$user || !($user instanceof Landlord || $user->isAdmin())) {
            //     return response()->json(['message' => 'Unauthorized'], 403);
            // }
    
            $ticket = MaintenanceTicket::findOrFail($id);
    
            // Only allow updating from open to closed
            // if ($ticket->status !== 'open') {
            //     return response()->json(['message' => 'Ticket can only be closed if it is open.'], 400);
            // }
    
            $ticket->status = 'closed';
            $ticket->save();
    
            // Notify the tenant about the status update
            $tenant = Tenant::findOrFail($ticket->tenant_id);
            Mail::send('ticket_status_update', [
                'ticket' => $ticket,
                'tenant' => $tenant,
            ], function ($message) use ($tenant) {
                $message->from('info@landlordtenant.com', 'LandlordTenant');
                $message->to($tenant->email)->subject('Maintenance Ticket Status Updated');
            });
    
            return response()->json($ticket);

        }catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    // Remove the specified maintenance ticket from storage
    public function destroy($id)
    {
        $ticket = MaintenanceTicket::findOrFail($id);
        $ticket->delete();

        return response()->json(['message' => 'Ticket deleted successfully']);
    }
}
