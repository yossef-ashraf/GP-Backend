<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['parent_id', 'data', 'image'];

    // Appended attributes
    protected $appends = ['image_url', 'items_count'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'product_categories');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Accessor for image_url
    public function getImageUrlAttribute()
    {
        return $this->image
            ? $this->image
            : null;
    }

    // Accessor for items_count
    public function getItemsCountAttribute()
    {
        return $this->products()->count();
    }
}
