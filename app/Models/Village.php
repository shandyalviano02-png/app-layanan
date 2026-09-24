<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Village extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'district_id',
        'code',
        'name',
    ];

    /**
     * The district that owns this village.
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Users assigned to this village.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Service requests originating from this village.
     */
    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    /**
     * Rehabilitation clients residing in this village.
     */
    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    /**
     * Complaints filed for incidents in this village.
     */
    public function complaints(): HasMany
    {
        return $this->hasMany(Complaint::class);
    }
}
