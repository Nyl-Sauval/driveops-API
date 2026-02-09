<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
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
     * Get the count of vehicles for a specific user.
     * GET /api/users/{user}/vehicles/count
     */
    // // GET /api/users/{user}/vehicles/count
    public function getNumberOfVehiclesByUser(User $user)
    {
        // Sécurité: Assurez-vous que l'utilisateur est autorisé à voir ce compte.
        $this->authorize('viewVehicles', $user);

        // Utilisation de la méthode count() d'Eloquent, qui traduit en un simple "SELECT COUNT(*) FROM vehicules WHERE user_id = ?"
        $count = Vehicule::where('user_id', $user->id)->count();

        // Retournez un JSON propre.
        return response()->json([
            'count' => $count,
            'user_id' => $user->id, // Optionnel, pour contexte
        ]);
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
        // 1. Start the query on the Maintenance model
        $query = Maintenance::query();

        // 2. Condition: Maintenances must be linked to a vehicle belonging to the user
        $query->whereHas('vehicles', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        // 3. Main Condition: FUTURE Maintenance (Date OR Mileage)
        $query->where(function ($q) {
            // Condition 3A: Future date
            $q->where('scheduled_date', '>', now());

            // Condition 3B: OR Future Mileage
            // We check if there is at least one linked VEHICLE for which
            // the maintenance's scheduled_mileage is greater than the current mileage.
            $q->orWhereExists(function ($subQuery) {
                    $subQuery->selectRaw(1)
                        ->from('vehicules as v')
                        // Join the pivot table to link the current maintenance (maintenances.id) to the vehicle (v.id)
                        ->join('maintenance_vehicule as mv', 'mv.vehicule_id', '=', 'v.id')
                        ->whereColumn('mv.maintenance_id', 'maintenances.id') // <-- Correlation key
                        // The condition is that the maintenance's scheduled mileage
                        // is greater than the vehicle's current mileage.
                        ->whereColumn('maintenances.scheduled_mileage', '>', 'v.mileage');
                }
                );
            });

        // 4. Execution and Eager Loading of Relationships
        $maintenances = $query->with(['vehicles', 'invoices'])->get();

        return response()->json($maintenances);
    }

    /**
     * Get the list of late maintenance records for a specific user.
     */
    // // GET /api/users/{userId}/maintenance/late
    public function lateMaintenancesByUser(string $userId)
    {
        // 1. Start the query on the Maintenance model
        $query = Maintenance::query();

        // 2. Condition: Maintenances must be linked to a vehicle belonging to the user
        $query->whereHas('vehicles', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });

        // 3. Main Condition: LATE Maintenance (Date OR Mileage)
        $query->where(function ($q) {
            // Condition 3A: Late date
            $q->where('scheduled_date', '<', now());

            // Condition 3B: OR Late Mileage
            // We check if there is at least one linked VEHICLE for which
            // the maintenance's scheduled_mileage is less than or equal to the current mileage.
            $q->orWhereExists(function ($subQuery) {
                    $subQuery->selectRaw(1)
                        ->from('vehicules as v')
                        // Join the pivot table to link the current maintenance (maintenances.id) to the vehicle (v.id)
                        ->join('maintenance_vehicule as mv', 'mv.vehicule_id', '=', 'v.id')
                        ->whereColumn('mv.maintenance_id', 'maintenances.id') // <-- Correlation key
                        // The condition is that the maintenance's scheduled mileage
                        // is less than or equal to the vehicle's current mileage.
                        ->whereColumn('maintenances.scheduled_mileage', '<=', 'v.mileage');
                }
                );
            });

        // 4. Execution and Eager Loading of Relationships
        $maintenances = $query->with(['vehicles', 'invoices'])->get();

        return response()->json($maintenances);
    }


    /**
     * Get unified dashboard data for a specific user.
     * Consolidates vehicles, maintenances, and invoices data in a single response.
     * 
     * @param User $user
     * @return \Illuminate\Http\JsonResponse
     */
    // GET /api/users/{user}/dashboard
    public function getDashboard(User $user)
    {
        // Authorization: User can only access their own dashboard (or admin can access any)
        $this->authorize('viewVehicles', $user);

        // 1. Get vehicles with count
        $vehicles = Vehicule::where('user_id', $user->id)->get();
        $vehicleCount = $vehicles->count();

        // 2. Count upcoming maintenances (future date OR future mileage)
        $upcomingQuery = Maintenance::query();
        $upcomingQuery->whereHas('vehicles', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
        $upcomingQuery->where(function ($q) {
            // Future date
            $q->where('scheduled_date', '>', now());

            // OR Future Mileage
            $q->orWhereExists(function ($subQuery) {
                    $subQuery->selectRaw(1)
                        ->from('vehicules as v')
                        ->join('maintenance_vehicule as mv', 'mv.vehicule_id', '=', 'v.id')
                        ->whereColumn('mv.maintenance_id', 'maintenances.id')
                        ->whereColumn('maintenances.scheduled_mileage', '>', 'v.mileage');
                }
                );
            });
        $upcomingCount = $upcomingQuery->count();

        // 3. Count late maintenances (past date OR past mileage)
        $lateQuery = Maintenance::query();
        $lateQuery->whereHas('vehicles', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        });
        $lateQuery->where(function ($q) {
            // Late date
            $q->where('scheduled_date', '<', now());

            // OR Late Mileage
            $q->orWhereExists(function ($subQuery) {
                    $subQuery->selectRaw(1)
                        ->from('vehicules as v')
                        ->join('maintenance_vehicule as mv', 'mv.vehicule_id', '=', 'v.id')
                        ->whereColumn('mv.maintenance_id', 'maintenances.id')
                        ->whereColumn('maintenances.scheduled_mileage', '<=', 'v.mileage');
                }
                );
            });
        $lateCount = $lateQuery->count();

        // 4. Count invoices for this user
        $invoiceCount = Invoice::whereHas('vehicles.user', function ($query) use ($user) {
            $query->where('id', $user->id);
        })->count();

        // 5. Return unified response
        return response()->json([
            'vehicles' => [
                'count' => $vehicleCount,
                'list' => $vehicles
            ],
            'maintenances' => [
                'upcoming' => $upcomingCount,
                'late' => $lateCount
            ],
            'invoices' => [
                'count' => $invoiceCount
            ]
        ], 200);
    }

    /**
     * Get the invoices for a specific user
     */
    // // GET /api/users/{userId}/invoices
    public function invoicesByUser($userId)
    {
        $invoices = Invoice::whereHas('vehicles.user', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->with(['vehicles', 'maintenances'])->get();

        return response()->json($invoices);
    }
}
