<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'nik',
        'work_unit_id',
        'district_id',
        'village_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Work unit / bidang / seksi user belongs to.
     */
    public function workUnit(): BelongsTo
    {
        return $this->belongsTo(WorkUnit::class);
    }

    /**
     * District area assigned to user (e.g. Kecamatan operator).
     */
    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }

    /**
     * Village area assigned to user (e.g. Desa / Kelurahan operator).
     */
    public function village(): BelongsTo
    {
        return $this->belongsTo(Village::class);
    }

    /**
     * Service requests submitted by this user.
     */
    public function submittedServiceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'submitter_id');
    }

    /**
     * Service requests handled by this user.
     */
    public function handledServiceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class, 'officer_id');
    }

    /**
     * DTSEN certificates checked by this user.
     */
    public function checkedDtsenCertificates(): HasMany
    {
        return $this->hasMany(DtsenCertificate::class, 'checker_id');
    }

    /**
     * DTSEN certificates signed by this user.
     */
    public function signedDtsenCertificates(): HasMany
    {
        return $this->hasMany(DtsenCertificate::class, 'signer_id');
    }

    /**
     * PBI recommendations signed by this user.
     */
    public function signedPbiReactivations(): HasMany
    {
        return $this->hasMany(PbiReactivation::class, 'signer_id');
    }

    /**
     * Approvals recorded by this user.
     */
    public function approvals(): HasMany
    {
        return $this->hasMany(Approval::class, 'approver_id');
    }

    /**
     * Complaints submitted by this user.
     */
    public function reportedComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'reporter_id');
    }

    /**
     * Complaints handled by this user.
     */
    public function handledComplaints(): HasMany
    {
        return $this->hasMany(Complaint::class, 'officer_id');
    }

    /**
     * Rehabilitation cases handled by this user.
     */
    public function rehabilitationCases(): HasMany
    {
        return $this->hasMany(RehabilitationCase::class, 'officer_id');
    }

    /**
     * Client assessments conducted by this user.
     */
    public function assessments(): HasMany
    {
        return $this->hasMany(Assessment::class, 'officer_id');
    }

    /**
     * Referrals handled by this user.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class, 'officer_id');
    }

    /**
     * Monitoring records authored by this user.
     */
    public function monitoringRecords(): HasMany
    {
        return $this->hasMany(MonitoringRecord::class, 'officer_id');
    }

    /**
     * Information pages managed by this user.
     */
    public function managedInformationPages(): HasMany
    {
        return $this->hasMany(InformationPage::class, 'manager_id');
    }

    /**
     * Status histories logged by this user.
     */
    public function statusHistories(): HasMany
    {
        return $this->hasMany(StatusHistory::class);
    }
}
