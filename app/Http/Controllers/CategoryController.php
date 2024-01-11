<?php

namespace App\Http\Controllers;

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
        return $this->response("done!!", $this->categoryService->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'content' => 'string|nullable',
           /* 'main_id' => 'required|integer|exists:categories,id',*/
            'discount' => 'integer',
        ]);

        $category = $this->categoryService->store($data);
        return $this->response("New category added successfully", $category);
    }
}
