<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'date',
        'amount',
        'description',
        'file_path',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Get the vehicles associated with the invoice.
     */
    public function vehicles()
    {
        return $this->belongsToMany(Vehicule::class, 'invoice_vehicule', 'invoice_id', 'vehicule_id')
                    ->withPivot('id', 'created_at', 'updated_at');
    }

    /**
     * Get the maintenances associated with the invoice.
     */
    public function maintenances()
    {
        return $this->belongsToMany(Maintenance::class, 'invoice_maintenance', 'invoice_id', 'maintenance_id')
                    ->withPivot('id', 'created_at', 'updated_at');
    }
}
