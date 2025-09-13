<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    public const TYPE_MILEAGE = 'mileage';
    public const TYPE_TIME = 'time';
    public const TYPE_ONE_TIME = 'one_time';

    public const TYPES = [
        self::TYPE_MILEAGE,
        self::TYPE_TIME,
        self::TYPE_ONE_TIME,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'description',
        'scheduled_date',
        'scheduled_mileage',
        'done',
        'done_date',
        'done_mileage',
        'cost',
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
     * Get the vehicles associated with the maintenance.
     */
    public function vehicles()
    {
        return $this->belongsToMany(Vehicule::class, 'maintenance_vehicule', 'maintenance_id', 'vehicule_id')
                    ->withPivot('id', 'created_at', 'updated_at');
    }

    /**
     * Get the invoices associated with the maintenance.
     */
    public function invoices()
    {
        return $this->belongsToMany(Invoice::class, 'invoice_maintenance', 'maintenance_id', 'invoice_id')
                    ->withPivot('id', 'created_at', 'updated_at');
    }
}
