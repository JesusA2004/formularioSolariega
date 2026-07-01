<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Database\Factories\RequestAttachmentFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $request_id
 * @property string $original_name
 * @property string $file_name
 * @property string $file_path
 * @property string|null $mime_type
 * @property int $size
 * @property CarbonInterface|null $created_at
 * @property CarbonInterface|null $updated_at
 */
#[Fillable([
    'request_id',
    'original_name',
    'file_name',
    'file_path',
    'mime_type',
    'size',
])]
class RequestAttachment extends Model
{
    /** @use HasFactory<RequestAttachmentFactory> */
    use HasFactory;

    /**
     * @return BelongsTo<Request, $this>
     */
    public function request(): BelongsTo
    {
        return $this->belongsTo(Request::class);
    }

    public function isImage(): bool
    {
        return str_starts_with((string) $this->mime_type, 'image/');
    }

    public function humanSize(): string
    {
        $bytes = $this->size;

        if ($bytes < 1024) {
            return $bytes.' B';
        }

        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1).' KB';
        }

        return round($bytes / (1024 * 1024), 1).' MB';
    }
}
