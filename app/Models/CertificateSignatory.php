<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class CertificateSignatory extends Model
{
    use HasFactory, HasUuid;

    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];

    protected $casts = [
        'active_from' => 'date',
        'active_until' => 'date',
    ];

    public static function activeAt(Carbon $date): \Illuminate\Database\Eloquent\Collection
    {
        return static::query()
            ->where('active_from', '<=', $date->toDateString())
            ->where(function ($q) use ($date) {
                $q->whereNull('active_until')
                    ->orWhere('active_until', '>=', $date->toDateString());
            })
            ->orderBy('sort_order')
            ->get();
    }

    public function signatureDataUri(): ?string
    {
        if (!$this->signature_image) {
            return null;
        }

        $path = Storage::disk('public')->path($this->signature_image);

        if (!file_exists($path)) {
            return null;
        }

        $mime = mime_content_type($path) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($path));
    }
}
