<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Item;
use Throwable;

class ItemController extends Controller
{
    public function index()
    {
        return json_encode($this->items());
    }

    private function items()
    {
        $items = Item::select(
            DB::raw("items.*"),
            DB::raw("users.firstname as update_user")
        )
            ->join('users', 'items.update_user', '=', 'users.id')
            ->where('items.is_deleted', '=', 0)
            ->orderBy('items.updated_at', 'desc')
            ->get();
        return $items;
    }

    public function show($id)
    {
        $items = Item::select(
            DB::raw("items.*"),
            DB::raw("users.firstname as update_user")
        )
            ->join('users', 'items.update_user', '=', 'users.id')
            ->where('items.is_deleted', '=', 0)
            ->where('items.id', $id)
            ->first();
        return json_encode($items);
    }

    public function store(Request $request)
    {
        $rules = $this->rules();
        $this->validate($request, $rules[0], $rules[0]);

        try {
            DB::beginTransaction();

            $item = new Item();

            switch ($request->item_category) {
                case "RAW MATERIAL":
                    $item->item_category = $request->item_category;
                    $item->item_type = $request->item_type;
                    $item->item_code = $request->item_code;
                    $item->item_desc = $request->item_desc;
                    $item->item = $request->item;
                    $item->schedule_class = $request->schedule_class;
                    $item->alloy = $request->alloy;
                    $item->size = $request->size;
                    $item->weight = $request->weight;

                    break;
                case "CRUDE":
                    $item->item_category = $request->item_category;
                    $item->item_type = $request->item_type;
                    $item->item_code = $request->item_code;
                    $item->item_desc = $request->item_desc;
                    $item->item = $request->item;
                    $item->schedule_class = $request->schedule_class;
                    $item->alloy = $request->alloy;
                    $item->size = $request->size;
                    $item->weight = $request->weight;
                    $item->cut_weight = $request->cut_weight;
                    $item->cut_length = $request->cut_length;
                    $item->cut_width = $request->cut_width;
                    $item->std_material_used = $request->std_material_used;
                    $item->finished_code = $request->finished_code;
                    $item->finished_desc = $request->finished_desc;
                    break;
                default:
                    $item->item_category = $request->item_category;
                    $item->item_type = $request->item_type;
                    $item->item_code = $request->item_code;
                    $item->item_desc = $request->item_desc;
                    $item->item = $request->item;
                    $item->schedule_class = $request->schedule_class;
                    $item->alloy = $request->alloy;
                    $item->size = $request->size;
                    $item->weight = $request->weight;
                    $item->cut_weight = $request->cut_weight;
                    $item->cut_length = $request->cut_length;
                    $item->cut_width = $request->cut_width;
                    $item->std_material_used = $request->std_material_used;
                    break;
            }

            $item->create_user = $request->user_id;
            $item->update_user = $request->user_id;

            if ($item->save()) {
                DB::commit();
                return response([
                    'message' => "Item details was successfully saved.",
                    'status' => "success",
                    'data' => $this->items()
                ]);
            }
        } catch (Throwable $th) {
            DB::rollBack();
            return response([
                'message' => $th->getMessage(),
                'status' => "error"
            ]);
        }
    }

    private function rules()
    {
        $rules = [
            [
                'item_category' => 'required',
                'item_type' => 'required',
                'item_desc' => 'required',
                'item' => 'required',
                'weight' => 'numeric',
                'cut_weight' => 'numeric',
                'cut_length' => 'numeric',
                'cut_width' => 'numeric',
                'item_code' => 'required|unique:items',
            ],
            [
                'item_category.required' => "Item Ctegory field is required.",
                'item_type.required' => "Input Type field is required.",
                'item_desc.required' => "Item Description field is required.",
                'item.required' => "Item field is required.",
                'item_code.required' => "Item Code field is required.",
                'item_code.unique' => "This Item code was already added.",
                'weight.numeric' => "Weight field must be a number",
                'cut_weight.numeric' => "Cut Weight field must be a number",
                'cut_length.numeric' => "Cut Length field must be a number",
                'cut_width.numeric' => "Cut Width field must be a number"
            ]
        ];

        return $rules;
    }

    public function update(Request $request, $id)
    {
        try {
            $item = Item::find($id);

            DB::beginTransaction();

            switch ($request->item_category) {
                case "RAW MATERIAL":
                    $item->id = $request->id;
                    $item->item_type = $request->item_type;
                    $item->item_code = $request->item_code;
                    $item->item_desc = $request->item_desc;
                    $item->item = $request->item;
                    $item->schedule_class = $request->schedule_class;
                    $item->alloy = $request->alloy;
                    $item->size = $request->size;
                    $item->weight = $request->weight;
                    break;
                case "FINISHED GOODS":
                    $item->id = $request->id;
                    $item->item_type = $request->item_type;
                    $item->item_code = $request->item_code;
                    $item->item_desc = $request->item_desc;
                    $item->item = $request->item;
                    $item->schedule_class = $request->schedule_class;
                    $item->alloy = $request->alloy;
                    $item->size = $request->size;
                    $item->weight = $request->weight;
                    $item->cut_weight = $request->cut_weight;
                    $item->cut_length = $request->cut_length;
                    $item->cut_width = $request->cut_width;
                    $item->std_material_used = $request->std_material_used;
                    break;
                default:
                    $item->id = $request->id;
                    $item->item_type = $request->item_type;
                    $item->item_code = $request->item_code;
                    $item->item_desc = $request->item_desc;
                    $item->item = $request->item;
                    $item->schedule_class = $request->schedule_class;
                    $item->alloy = $request->alloy;
                    $item->size = $request->size;
                    $item->weight = $request->weight;
                    $item->cut_weight = $request->cut_weight;
                    $item->cut_length = $request->cut_length;
                    $item->cut_width = $request->cut_width;
                    $item->std_material_used = $request->std_material_used;
                    $item->finished_code = $request->finished_code;
                    $item->finished_desc = $request->finished_desc;
                    break;
            }

            $item->update_user = $request->user_id;

            if ($item->update()) {
                DB::commit();
                return response([
                    'message' => "Item details was successfully saved.",
                    'status' => "success",
                    'data' => $this->items()
                ]);
            }
        } catch (Throwable $th) {
            DB::rollBack();
            return response([
                'message' => $th->getMessage(),
                'status' => "error"
            ]);
        }

        return response([
            'message' => "Saving Item details was unsuccessful.",
            'status' => "warning"
        ]);
    }

    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();

            $delete = Item::whereIn('id', $request->id)->update([
                'is_deleted' => 1,
                'update_user' => $request->user_id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($delete) {
                DB::commit();
                return response([
                    'message' => "Item details was successfully deleted.",
                    'status' => "success",
                    'data' => $this->items()
                ]);
            }
        } catch (Throwable $th) {
            DB::rollBack();
            return response([
                'message' => $th->getMessage(),
                'status' => "error"
            ]);
        }

        return response([
            'message' => "Deleting Item details was unsuccessful.",
            'status' => "warning"
        ]);
    }

    public function item_status()
    {
        $raw_materials = Item::where('item_category', 'RAW MATERIAL')->where('is_deleted', 0)->count();
        $crude = Item::where('item_category', 'CRUDE')->where('is_deleted', 0)->count();
        $finished_goods = Item::where('item_category', 'FINISHED GOODS')->where('is_deleted', 0)->count();

        return response([
            'raw_materials' => $raw_materials,
            'crude' => $crude,
            'finished_goods' => $finished_goods
        ]);
    }
}
