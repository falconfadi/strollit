<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'price',
        'category_id',
        'discount'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class,'category_id');
    }

}
