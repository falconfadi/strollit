<?php

namespace App\Services;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CategoryService
{
    public function get()
    {
        //get all categories with item by user
        $categories = Category::with('items')->where('user_id',\Auth::id())->get();
        $categoriesByDiscount = array();
        foreach ($categories as $category){
            if($category->level == 0){
                array_push($categoriesByDiscount, $category);
            }else{
                $parent = $category->mainCategory;
                $category->discount = $this->getDiscount($category, $parent);
                array_push($categoriesByDiscount, $category);
            }
            if($category->items){
                foreach ($category->items as $item){
                    if(is_null($item->discount)){
                        $item->discount = $category->discount;
                    }
                }
            }
        }
        return $categoriesByDiscount;
    }

    public function storeMain($userId)
    {
        $existCategory = Category::where('user_id',$userId)->whereNull('main_id')->first();
        if (!$existCategory)
        {
            return Category::create([
                'title' => 'Main category',
                'content' => '',
                'main_id' => null,
                'user_id' => $userId,
                'level' => 0
            ]);
        }else{
            return null;
        }
    }
    public function store($data)
    {
        $userId = \Auth::id();
        $mainCategory = Category::where('user_id',$userId)->where('level',0)->first();
        //build root if not exists
        if(!$mainCategory){
            $mainCategory = $this->storeMain($userId);
        }
        if ($mainCategory->user_id != $userId)
            throw new UnauthorizedException('Not authorized',403);
//        if ($mainCategory->items->count()>0)
//            throw new BadRequestException('This category has items');
        if ($mainCategory->level >= 4)
            throw new BadRequestException('maximum level of categories');
        $newLevel = $mainCategory->level;
        $newLevel++;
        return Category::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'main_id' => $mainCategory->id,
            'user_id' => $userId,
            'level' => $newLevel,
            'discount' => $data['discount']??null
        ]);
    }

    public function update($data, $id)
    {
        $category = Category::find($id);
        if($category){
            if ($category->user_id != \Auth::id())
                throw new UnauthorizedException('Not authorized',403);
            return $category->update($data);
        }
    }

    public function getById($id)
    {
        $category = Category::where('user_id',\Auth::id())->where('id',$id)->first();
        return $category;
    }

    //get discount recursively
    public function getDiscount(&$category, $parent){
        if( !is_null($category->discount)){
            return $category->discount;
        }else{
            while(!is_numeric($category->discount)){
                if( !is_numeric($parent->discount)){
                    return $this->getDiscount($category,  $parent->mainCategory);
                }else{
                    return $parent->discount;
                }
            }
        }
    }


}
