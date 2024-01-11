<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected  $table = 'categories';
    use HasFactory;
    protected $fillable = [
        'title',
        'content',
        'main_id',
        'user_id',
        'level',
        'discount'
    ];

    public function mainCategory()
    {
        return $this->belongsTo(Category::class,'main_id');
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'main_id');
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }
}
