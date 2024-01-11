<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private $categoryService;
    public function __construct(CategoryService $categoryService)
    {
        parent::__construct();
        $this->categoryService = $categoryService;
    }

    public function index()
    {
        $categories = $this->categoryService->get();
        return $this->response("done!!", $categories);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'string|nullable',
            'discount' => 'numeric',
        ]);

        $category = $this->categoryService->store($data);
        return $this->response("New category added successfully", $category);
    }

    public function update(Request $request, $categoryId)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'string|nullable',
            'discount' => 'numeric',
        ]);
        $this->categoryService->update($data, $categoryId);
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
