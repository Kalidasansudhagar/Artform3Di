<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['title', 'category', 'summary', 'image', 'is_active', 'sort_order'])]
class Project extends Model
{
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /** The array shape the home and services views were built around. */
    public function toCatalogArray(): array
    {
        return [
            'title' => $this->title,
            'category' => $this->category,
            'text' => $this->summary,
            'image' => $this->image ?: 'assets/images/projects/enclosure.svg',
        ];
    }
}
