<?php

namespace App\Models;

use App\Enums\InformationCategory;
use App\Enums\PublishStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformationPage extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'slug',
        'category',
        'service_type_id',
        'description',
        'requirements',
        'procedure',
        'service_hours',
        'location',
        'contact',
        'publish_status',
        'published_at',
        'manager_id',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'category' => InformationCategory::class,
            'publish_status' => PublishStatus::class,
            'published_at' => 'datetime',
        ];
    }

    /**
     * Service type linked to this information page, if any.
     */
    public function serviceType(): BelongsTo
    {
        return $this->belongsTo(ServiceType::class);
    }

    /**
     * Officer managing this information page.
     */
    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Downloadable forms available on this page.
     */
    public function downloadableForms(): HasMany
    {
        return $this->hasMany(DownloadableForm::class);
    }

    /**
     * FAQs associated with this information page.
     */
    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class)->orderBy('sort_order');
    }

    /**
     * Page visit statistics.
     */
    public function pageVisits(): HasMany
    {
        return $this->hasMany(PageVisit::class);
    }
}
