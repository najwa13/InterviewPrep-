<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Concept extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['domain_id', 'title', 'explanation', 'difficulty', 'status'];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function generatedQuestions(): HasMany
    {
        return $this->hasMany(GeneratedQuestion::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'to_review' => 'À revoir',
            'in_progress' => 'En cours',
            'mastered' => 'Maîtrisé',
        };
    }

    public function getDifficultyLabelAttribute(): string
    {
        return match ($this->difficulty) {
            'junior' => 'Junior',
            'mid' => 'Mid',
            'senior' => 'Senior',
        };
    }
}