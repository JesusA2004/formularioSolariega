<?php

namespace App\Models;

use App\Enums\RequestStatus;
use App\Enums\RequestType;
use App\Enums\UrgencyLevel;
use App\Services\FolioGenerator;
use Carbon\CarbonInterface;
use Database\Factories\RequestFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $uuid
 * @property string $folio
 * @property RequestType $request_type
 * @property bool $is_anonymous
 * @property string|null $full_name
 * @property string $department
 * @property string $location
 * @property CarbonInterface|null $incident_date
 * @property string $description
 * @property string|null $involved_people
 * @property UrgencyLevel $urgency_level
 * @property bool $has_evidence
 * @property bool $wants_follow_up
 * @property string|null $contact_info
 * @property bool $accepted_terms
 * @property RequestStatus $status
 * @property string|null $internal_notes
 * @property int|null $reviewed_by
 * @property CarbonInterface|null $reviewed_at
 * @property CarbonInterface|null $closed_at
 * @property string|null $ip_address
 * @property CarbonInterface|null $created_at
 * @property CarbonInterface|null $updated_at
 */
#[Fillable([
    'request_type',
    'is_anonymous',
    'full_name',
    'department',
    'location',
    'incident_date',
    'description',
    'involved_people',
    'urgency_level',
    'has_evidence',
    'wants_follow_up',
    'contact_info',
    'accepted_terms',
    'ip_address',
])]
class Request extends Model
{
    /** @use HasFactory<RequestFactory> */
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (Request $request): void {
            if (empty($request->uuid)) {
                $request->uuid = (string) Str::uuid();
            }

            if (empty($request->folio)) {
                $request->folio = FolioGenerator::next();
            }
        });
    }

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'request_type' => RequestType::class,
            'urgency_level' => UrgencyLevel::class,
            'status' => RequestStatus::class,
            'is_anonymous' => 'boolean',
            'has_evidence' => 'boolean',
            'wants_follow_up' => 'boolean',
            'accepted_terms' => 'boolean',
            'incident_date' => 'date',
            'reviewed_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    /**
     * @return HasMany<RequestAttachment, $this>
     */
    public function attachments(): HasMany
    {
        return $this->hasMany(RequestAttachment::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    /**
     * Shared filtering used by the admin listing and the reports module.
     *
     * @param  Builder<Request>  $query
     * @param  array<string, mixed>  $filters
     * @return Builder<Request>
     */
    public function scopeFilter(Builder $query, array $filters): Builder
    {
        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function (Builder $q) use ($search) {
                $q->where('folio', 'like', "%{$search}%")
                    ->orWhere('full_name', 'like', "%{$search}%")
                    ->orWhere('contact_info', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('involved_people', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['request_type'])) {
            $query->where('request_type', $filters['request_type']);
        }

        if (! empty($filters['department'])) {
            $query->where('department', $filters['department']);
        }

        if (array_key_exists('has_evidence', $filters) && $filters['has_evidence'] !== null && $filters['has_evidence'] !== '') {
            $query->where('has_evidence', filter_var($filters['has_evidence'], FILTER_VALIDATE_BOOLEAN));
        }

        if (! empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (! empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }
}
