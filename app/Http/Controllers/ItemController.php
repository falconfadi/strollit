<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use App\Services\CategoryService;
use App\Services\ItemService;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;

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

        ]);

        $category = $this->itemService->store($data);
        return $this->response("New Item added successfully", $category);
    }

    public function show($itemId){
        $item = $this->itemService->getById($itemId);
        return $this->response("Updated successfully",$item);
    }

    public function update(Request $request, $itemId)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'price' => 'required|numeric',
            'discount' => 'numeric',
        ]);
        $result = $this->itemService->update($data, $itemId);
        if($result['message']=="success")
            return $this->response("Updated successfully");
        else
            return $this->response($result['data']);

    }

    public function destroy($itemId) {
        $userId = \Auth::id();
        $item = Item::find($itemId);
        if($item ){
            if ($item->category->user_id != $userId)
                throw new UnauthorizedException('Not authorized',403);
            $item->delete();
            return $this->response("Deleted successfully #".$itemId);
        }else{
            return $this->response("Not Found!!");
        }


    }
}
