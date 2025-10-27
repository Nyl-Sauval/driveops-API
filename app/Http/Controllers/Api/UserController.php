<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // GET /api/users
    public function index()
    {
        $this->authorize('viewAny', User::class);

        return response([
            'message' => 'List of users',
            'data' => [
                User::all()
            ]
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    // POST /api/users
    public function store(Request $request)
    {
        $validated = $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    // GET /api/users/{id}
    public function show(string $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('view', $user);

        return response()->json($user, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    // POST /api/users/{id}
    public function update(Request $request, string $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('update', $user);

        $validated = $request->validate([
            'firstname' => 'sometimes|string|max:255',
            'lastname' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|string|min:8|confirmed',
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = bcrypt($validated['password']);
        }
        $user->update($validated);

        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    // DELETE /api/users/{id}
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);

        $this->authorize('delete', $user);

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ], 204);
    }


    /**
     * Get the list of vehicles for a specific user.
     */
    // // GET /api/users/{user}/vehicles
    public function getUserVehicles(User $user)
    {
        $this->authorize('viewVehicles', $user);

        $vehicles = Vehicule::where('user_id', $user->id)
            ->with(['user', 'maintenances', 'invoices'])
            ->get();

        return response()->json($vehicles);
    }

    /**
     * Get the list of maintenance records for a specific user.
     */
    // // GET /api/users/{userId}/maintenance
    public function maintenancesByUser(string $userId)
    {
        $maintenances = Maintenance::whereHas('vehicles.user', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->with(['vehicles', 'invoices'])->get();

        return response()->json($maintenances);
    }

    /**
     * Get the list of future maintenance records for a specific user.
     */
    // // GET /api/users/{userId}/maintenance/future
    public function futureMaintenancesByUser(string $userId)
    {
        $maintenances = Maintenance::whereHas('vehicles.user', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->where(function ($query) {
            $query->where('scheduled_date', '>', now())
                  ->orWhere('scheduled_mileage', '>', function ($subQuery) {
                      $subQuery->selectRaw('MAX(mileage)')
                               ->from('vehicules')
                               ->whereColumn('vehicules.id', 'maintenance_vehicule.vehicule_id');
                  });
        })->with(['vehicles', 'invoices'])->get();

        return response()->json($maintenances);
    }
}
