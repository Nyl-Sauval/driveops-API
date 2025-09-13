<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicule;
use Illuminate\Http\Request;

class VehiculeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // // GET /api/vehicules
    public function index()
    {
        $this->authorize('viewAny', Vehicule::class);
        return response()->json(
            Vehicule::with(['user', 'maintenances', 'invoices'])->get()
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    // // POST /api/vehicules
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|between:1900,2100',
            'mileage' => 'required|integer|min:0',
            'license_plate' => 'required|string|max:20|unique:vehicules',
            'user_id' => 'required|exists:users,id',
        ]);

        $vehicule = Vehicule::create($validated);
        return response()->json($vehicule, 201);
    }

    /**
     * Display the specified resource.
     */
    // // GET /api/vehicules/{id}
    public function show(string $id)
    {
        $vehicule = Vehicule::with(['user', 'maintenances', 'invoices'])->findOrFail($id);

        $this->authorize('view', $vehicule);

        return response()->json($vehicule);
    }

    /**
     * Update the specified resource in storage.
     */
    // // PUT /api/vehicules/{id}
    public function update(Request $request, string $id)
    {
        $vehicule = Vehicule::findOrFail($id);

        $this->authorize('update', $vehicule);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'brand' => 'sometimes|string|max:255',
            'model' => 'sometimes|string|max:255',
            'year' => 'sometimes|integer|between:1900,2100',
            'mileage' => 'sometimes|integer|min:0',
            'license_plate' => 'sometimes|string|max:20|unique:vehicules,license_plate,' . $id,
            'user_id' => 'sometimes|exists:users,id',
        ]);

        $vehicule->update($validated);
        return response()->json($vehicule);
    }

    /**
     * Remove the specified resource from storage.
     */
    // DELETE /api/vehicules/{id}
    public function destroy(string $id)
    {
        $vehicule = Vehicule::findOrFail($id);

        $this->authorize('delete', $vehicule);

        $vehicule->delete();

        return response()->json(null, 204);
    }
}
