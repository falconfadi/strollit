<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ItemService
{
    public function get()
    {
        $userId = \Auth::id();
        return Item::whereHas('category',function($subQ) use($userId) {
            $subQ->where('user_id', $userId);
        })->get();
    }

//    public function getByCategoryId($categoryId){
//        $userId = \Auth::id();
//        return Item::whereHas('category',function($subQ) use($userId, $categoryId) {
//            $subQ->where('user_id', $userId)
//            ->where('category_id', $categoryId);
//        })->get();
//    }

    public function store($data)
    {
        $userId = \Auth::id();
        $categoryId = $data['category_id'];
        $category = Category::find($categoryId);
        if($category){
            // id category belong to other user
            if ($category->user_id != $userId)
                throw new UnauthorizedException('Not authorized',403);
            // id category has subcategories
            if ($category->subcategories->count()>0)
                throw new BadRequestException('This category is not in the last level');
            return Item::create([
                'title'=> $data['title'],
                'price'=> $data['price'],
                'category_id'=> $categoryId,
                'discount'=> $data['discount']??null
            ]);
        }
    }

    public function update($data, $id)
    {
        $userId = \Auth::id();
        $item = Item::find($id);
        if($item){
            // id item belong to other user
            if ($item->category->user_id != $userId)
                throw new UnauthorizedException('Not authorized',403);
            return [
                "message" => "success",
                "data" => $item->update($data)];
        }else{
            return [
                "message"=> "fail",
                "data"=> "Not Found!!" ];
        }
    }
    public function getById($id)
    {
        return Item::find($id);
    }


}
