<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Material extends Model
{
    use HasFactory, HasUuid;

    public const TYPES = ['pdf', 'ppt', 'video'];
    public const VISIBILITIES = ['public', 'private'];
    public const STATUSES = ['active', 'inactive'];

    public $incrementing = false;   
    protected $keyType = 'string';

    protected $guarded = [];

    protected $fillable = [
        'topic_id',
        'uploader_id',
        'name',
        'type',
        'path',
        'external_url',
        'visibility',
        'sort_order',
        'status',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function topic()
    {
        return $this->belongsTo(Topic::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploader_id');
    }

    public function materialProgresses()
    {
        return $this->hasMany(MaterialProgress::class);
    }


    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('visibility', 'public');
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }

    public function isDocument(): bool
    {
        return in_array($this->type, ['pdf', 'ppt'], true);
    }

    public function setTypeAttribute($value): void
    {
        $this->attributes['type'] = strtolower(trim((string) $value));
    }

    public function setVisibilityAttribute($value): void
    {
        $this->attributes['visibility'] = strtolower(trim((string) $value));
    }

    public function setStatusAttribute($value): void
    {
        $this->attributes['status'] = strtolower(trim((string) $value));
    }


    public function materials()
    {
        return $this->hasMany(\App\Models\Material::class)->orderBy('sort_order')->orderBy('created_at');
    }

    public function visibleMaterials()
    {
        return $this->materials()->where('status', 'active')->where('visibility', 'public');
    }
}