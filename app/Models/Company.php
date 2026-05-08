<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;

class Company extends Model
{
    use HasUuid;

    protected $guarded = [];
}