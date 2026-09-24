<?php

namespace App\Models;

use App\Enums\HandlingType;
use App\Enums\RehabilitationCaseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RehabilitationCase extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'case_number',
        'client_id',
        'service_request_id',
        'complaint_id',
        'officer_id',
        'handling_type',
        'status',
        'handling_result',
        'received_at',
        'closed_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'handling_type' => HandlingType::class,
            'status' => RehabilitationCaseStatus::class,
            'received_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * The client being served.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Originating service request if applicable.
     */
    public function serviceRequest(): BelongsTo
    {
        return $this->belongsTo(ServiceRequest::class);
    }

    /**
     * Originating complaint if applicable.
     */
    public function complaint(): BelongsTo
    {
        return $this->belongsTo(Complaint::class);
    }

    /**
     * The social worker / officer assigned.
     */
    public function officer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'officer_id');
    }

    /**
     * Assessments performed for this case.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class);
    }

    /**
     * Referrals made for this case.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
    }

    /**
     * Monitoring records logged for this case.
     */
    public function monitoringRecords(): HasMany
    {
        return $this->hasMany(MonitoringRecord::class);
    }

    /**
     * Polymorphic status change logs.
     */
    public function statusHistories(): MorphMany
    {
        return $this->morphMany(StatusHistory::class, 'statusable');
    }

    /**
     * Polymorphic dispositions for this case.
     */
    public function dispositions(): MorphMany
    {
        return $this->morphMany(Disposition::class, 'dispositionable');
    }
}
