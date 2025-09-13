<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Maintenance;
use Illuminate\Http\Request;

class MaintenanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // // GET /api/maintenance
    public function index()
    {
        return response()->json(Maintenance::with(['vehicles', 'invoices'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    // // POST /api/maintenance
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:' . implode(',', Maintenance::TYPES),
            'description' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'scheduled_mileage' => 'nullable|integer|min:0',
            'done' => 'boolean',
            'done_date' => 'nullable|date',
            'done_mileage' => 'nullable|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'vehicle_ids' => 'array',
            'vehicle_ids.*' => 'exists:vehicules,id',
        ]);

        $maintenance = Maintenance::create($validated);

        if (!empty($validated['vehicle_ids'])) {
            $maintenance->vehicles()->attach($validated['vehicle_ids']);
        }

        return response()->json($maintenance->load(['vehicles', 'invoices']), 201);
    }

    /**
     * Display the specified resource.
     */
    // // GET /api/maintenance/{id}
    public function show(string $id)
    {
        $maintenance = Maintenance::with(['vehicles', 'invoices'])->findOrFail($id);
        return response()->json($maintenance);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $maintenance = Maintenance::findOrFail($id);

        $validated = $request->validate([
            'type' => 'sometimes|in:' . implode(',', Maintenance::TYPES),
            'description' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'scheduled_mileage' => 'nullable|integer|min:0',
            'done' => 'boolean',
            'done_date' => 'nullable|date',
            'done_mileage' => 'nullable|integer|min:0',
            'cost' => 'nullable|numeric|min:0',
            'vehicle_ids' => 'array',
            'vehicle_ids.*' => 'exists:vehicules,id',
        ]);

        $maintenance->update($validated);

        if (array_key_exists('vehicle_ids', $validated)) {
            $maintenance->vehicles()->sync($validated['vehicle_ids']);
        }

        return response()->json($maintenance->load(['vehicles', 'invoices']));
    }

    /**
     * Remove the specified resource from storage.
     */
    // // DELETE /api/maintenance/{id}
    public function destroy(string $id)
    {
        $maintenance = Maintenance::findOrFail($id);
        $maintenance->vehicles()->detach();
        $maintenance->invoices()->detach();
        $maintenance->delete();

        return response()->json(null, 204);
    }

    /**
     * Get the list of maintenance records for a specific user.
     */
    // // GET /api/users/{userId}/maintenance
    public function userMaintenance(string $userId)
    {
        $maintenances = Maintenance::whereHas('vehicles.user', function ($query) use ($userId) {
            $query->where('id', $userId);
        })->with(['vehicles', 'invoices'])->get();

        return response()->json($maintenances);
    }

    /**
     * Get the list of maintenance records for a specific vehicle.
     */
    // // GET /api/vehicules/{vehiculeId}/maintenance
    public function vehiculeMaintenance(string $vehiculeId)
    {
        $maintenances = Maintenance::whereHas('vehicles', function ($query) use ($vehiculeId) {
            $query->where('id', $vehiculeId);
        })->with(['vehicles', 'invoices'])->get();

        return response()->json($maintenances);
    }
}
