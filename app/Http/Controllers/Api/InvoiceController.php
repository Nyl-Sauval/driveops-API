<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // // GET /api/invoices
    public function index()
    {
        return response()->json(Invoice::with(['vehicles', 'maintenances'])->get());
    }

    /**
     * Store a newly created resource in storage.
     */
    // // POST /api/invoices
    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'amount' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'file_path' => 'nullable|string',
            'vehicle_ids' => 'array',
            'vehicle_ids.*' => 'exists:vehicules,id',
            'maintenance_ids' => 'array',
            'maintenance_ids.*' => 'exists:maintenances,id',
        ]);

        $invoice = Invoice::create($validated);

        if (!empty($validated['vehicle_ids'])) {
            $invoice->vehicles()->attach($validated['vehicle_ids']);
        }

        if (!empty($validated['maintenance_ids'])) {
            $invoice->maintenances()->attach($validated['maintenance_ids']);
        }

        return response()->json($invoice->load(['vehicles', 'maintenances']), 201);
    }

    /**
     * Display the specified resource.
     */
    // // GET /api/invoices/{id}
    public function show(string $id)
    {
        $invoice = Invoice::with(['vehicles', 'maintenances'])->findOrFail($id);
        return response()->json($invoice);
    }

    /**
     * Update the specified resource in storage.
     */
    // // PUT /api/invoices/{id}
    public function update(Request $request, string $id)
    {
        $invoice = Invoice::findOrFail($id);

        $validated = $request->validate([
            'date' => 'sometimes|date',
            'amount' => 'sometimes|numeric|min:0',
            'description' => 'nullable|string',
            'file_path' => 'nullable|string',
            'vehicle_ids' => 'array',
            'vehicle_ids.*' => 'exists:vehicules,id',
            'maintenance_ids' => 'array',
            'maintenance_ids.*' => 'exists:maintenances,id',
        ]);

        $invoice->update($validated);

        if (array_key_exists('vehicle_ids', $validated)) {
            $invoice->vehicles()->sync($validated['vehicle_ids']);
        }

        if (array_key_exists('maintenance_ids', $validated)) {
            $invoice->maintenances()->sync($validated['maintenance_ids']);
        }

        return response()->json($invoice->load(['vehicles', 'maintenances']));
    }

    /**
     * Remove the specified resource from storage.
     */
    // // DELETE /api/invoices/{id}
    public function destroy(string $id)
    {
        $invoice = Invoice::findOrFail($id);
        $invoice->vehicles()->detach();
        $invoice->maintenances()->detach();
        $invoice->delete();

        return response()->json(null, 204);
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

    /**
     * Get the invoices for a specific vehicle
     */
    // // GET /api/vehicules/{vehiculeId}/invoices
    public function invoicesByVehicule($vehiculeId)
    {
        $invoices = Invoice::whereHas('vehicles', function ($query) use ($vehiculeId) {
            $query->where('id', $vehiculeId);
        })->with(['vehicles', 'maintenances'])->get();

        return response()->json($invoices);
    }

    /**
     * Get the invoices for a specific maintenance
     */
    // // GET /api/maintenances/{maintenanceId}/invoices
    public function invoicesByMaintenance($maintenanceId)
    {
        $invoices = Invoice::whereHas('maintenances', function ($query) use ($maintenanceId) {
            $query->where('id', $maintenanceId);
        })->with(['vehicles', 'maintenances'])->get();

        return response()->json($invoices);
    }

}
