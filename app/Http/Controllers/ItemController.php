<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use App\Services\ItemService;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    private $itemService;
    public function __construct(ItemService $itemService)
    {
        parent::__construct();
        $this->itemService = $itemService;
    }

    public function index()
    {
        $items = $this->itemService->get();
        return $this->response("done!!", $items);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'price' => 'required|numeric',
            'category_id' => 'required',
            'discount' => 'numeric',
        ]);

        $category = $this->itemService->store($data);
        return $this->response("New Item added successfully", $category);
    }

    public function update(Request $request, $itemId)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'string|nullable',
            'discount' => 'numeric',
        ]);
        $this->itemService->update($categoryId, $data);
        return $this->response("Updated successfully");
    }

    public function destroy($categoryId) {
        $category = Category::find($categoryId);
        if($category && $category->level >0){
            //if not main category
            $category->delete();
        }

        return $this->response("Deleted successfully #".$categoryId);
    }
}
