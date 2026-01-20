<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $fillable = ['name','slug','description','image','price','subtitle','code','featured'];

    public function getImageUrlAttribute()
    {
        if (empty($this->image)) {
            return asset('images/placeholder-200x200.png');
        }

        $publicPath = public_path('images/ateco/products/' . $this->image);
        if (file_exists($publicPath)) {
            return asset('images/ateco/products/' . $this->image);
        }

        if (Storage::disk('public')->exists('products/' . $this->image)) {
            return asset('storage/products/' . $this->image);
        }

        return asset('images/placeholder-200x200.png');
    }
}

