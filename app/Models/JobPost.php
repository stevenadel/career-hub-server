<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class JobPost extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'description',
        'requirements',
        'min_salary',
        'max_salary',
        'city',
        'country',
        'min_exp_years',
        'max_exp_years',
        'expires_at',
        'is_approved',
        'type',
        'remote_type',
        'experience_level'
    ];

    public function skills(): BelongsToMany
    {
        return $this
            ->belongsToMany(Skill::class)
            ->withTimestamps();
    }
}