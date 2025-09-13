<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'brand',
        'model',
        'year',
        'mileage',
        'license_plate',
        'user_id',
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
     * Get the user that owns the vehicle.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function maintenances()
    {
        return $this->belongsToMany(Maintenance::class, 'maintenance_vehicule', 'vehicule_id', 'maintenance_id')
            ->withPivot('id', 'created_at', 'updated_at');
    }

    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_vehicule', 'vehicule_id', 'invoice_id')
            ->withPivot('id', 'created_at', 'updated_at');
    }

}
