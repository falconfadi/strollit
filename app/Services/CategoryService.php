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
        $categories = Category::where('user_id',\Auth::id())->get();
        return $categories;
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
            'discount' => $data['discount']
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

    public function delete($id)
    {
        $category = Category::find($id);
        if ($category->user_id != \Auth::id())
            throw new UnauthorizedException('UnAuthorized',403);
        if ($category->parent_id == null)
            throw new UnauthorizedException('You can\'t delete the root category');
        return $category->delete();
    }
}
