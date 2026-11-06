<?php

namespace App\Models;

use App\Support\Catalog;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

#[Fillable(['name', 'slug', 'category', 'summary', 'description', 'price', 'badge', 'material', 'dimensions', 'weight', 'images', 'is_active', 'sort_order'])]
class Product extends Model
{
    protected function casts(): array
    {
        return [
            'images' => 'array',
            'price' => 'integer',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    public function getCategoryNameAttribute(): string
    {
        return Catalog::CATEGORIES[$this->category] ?? Str::headline($this->category);
    }

    /** The array shape the shop views and product card were built around. */
    public function toCatalogArray(): array
    {
        $images = array_values(array_filter($this->images ?? []));
        if (! $images) {
            $images = ['assets/images/collections/bespoke.svg'];
        }
        // The card and gallery expect at least three images; repeat the last one if fewer were uploaded.
        while (count($images) < 3) {
            $images[] = end($images);
        }

        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'category' => $this->category,
            'category_name' => $this->category_name,
            'price' => $this->price ?? 0,
            'price_label' => $this->price ? '₹'.number_format($this->price) : 'Price on request',
            'badge' => $this->badge,
            'material' => $this->material ?: 'Confirmed when you order',
            'dimensions' => $this->dimensions ?: 'On request',
            'weight' => $this->weight ?: '—',
            'images' => $images,
            'image' => $images[0],
            'image_url' => asset($images[0]),
            'url' => route('shop.show', $this->slug),
            'excerpt' => $this->summary ?: 'A 3D printed piece made in our Sholinganallur, Chennai studio, with painting and finishing available on selected parts.',
            'description' => $this->description,
            'model' => null,
        ];
    }
}
