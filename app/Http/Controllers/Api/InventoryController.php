<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\InventoryItem;
use App\Item;
use App\InventoryHistory;
use Throwable;

class InventoryController extends Controller
{
    public function index(Request $request)
    {
        $inventories = $this->inventory_items();
        return json_encode($inventories);
    }

    private function inventory_items()
    {
        return InventoryItem::join('items', 'items.id', '=', 'inventory_items.item_id')
            ->where("inventory_items.is_deleted", 0)
            ->orderBy('inventory_items.updated_at', 'desc')
            ->get([
                "inventory_items.*",
                DB::raw("items.id as item_id"),
                DB::raw("items.item_code as item_code"),
                DB::raw("items.item_desc as item_desc"),
                DB::raw("items.item_category as item_category"),
                DB::raw("items.item_type as item_type")
            ]);
    }

    public function get_item_types(Request $request)
    {
        $item_types = Item::distinct()->get(['item_type', 'item_category']);
        return json_encode($item_types);
    }

    public function get_warehouse()
    {
        $warehouses = InventoryItem::distinct()
            ->get([
                DB::raw("warehouse as value"),
                DB::raw("warehouse as text")
            ]);
        return json_encode($warehouses);
    }

    public function get_items(Request $request)
    {
        $item = Item::select('id', 'item_code', 'item_desc')
            ->where('item_code', $request->item_code)
            ->where('item_type', $request->item_type)
            ->distinct()->first();
        return json_encode($item);
    }

    public function store(Request $request)
    {
        $rules = $this->rules($request);
        $this->validate($request, $rules[0], $rules[1]);

        try {
            DB::beginTransaction();

            $inv = new InventoryItem();

            $inv->item_id = $request->item_id;
            $inv->warehouse = $request->warehouse;
            $inv->quantity = $request->quantity;
            $inv->weight = $request->weight;
            $inv->length = $request->length;
            $inv->width = $request->width;
            $inv->heat_no = $request->heat_no;
            $inv->lot_no = $request->lot_no;
            $inv->sc_no = $request->sc_no;
            $inv->supplier = $request->supplier;
            $inv->supplier_heat_no = $request->supplier_heat_no;
            $inv->material_used = $request->material_used;
            $inv->weight_received = $request->weight_received;
            $inv->is_excess = ($request->is_excess == false) ? 0 : 1;

            $inv->create_user = $request->user_id;
            $inv->update_user = $request->user_id;

            if ($inv->save()) {

                $hs = new InventoryHistory();
                $hs->inv_id = $inv->id;
                $hs->transaction_type = "RECEIVED";
                $hs->remarks = "Item received from Inventory Module";
                $hs->create_user = $request->user_id;

                if ($hs->save()) {
                    DB::commit();
                    return json_encode([
                        'message' => "Inventory Item details was successfully saved.",
                        'status' => "success",
                        'data' => $request->all()
                    ]);
                } else {
                    DB::rollBack();
                    return json_encode([
                        'message' => "Inventory Item data was not saved to the history.",
                        'status' => "failed",
                        'data' => $request->all()
                    ]);
                }
            } else {
                DB::rollBack();
                return json_encode([
                    'message' => "Inventory Item was not saved due to an error occurred.",
                    'status' => "failed",
                    'data' => $request->all()
                ]);
            }
        } catch (Throwable $th) {
            DB::rollBack();
            return json_encode([
                'message' => $th->getMessage(),
                'status' => "error"
            ]);
        }
    }

    private function rules($request)
    {
        $rules = [
            [
                'quantity' => 'required|numeric',
                'width' => 'numeric',
                'weight_received' => 'numeric',
                'warehouse' => 'required',
                //'heat_no' => 'required',
                'length' => [
                    'numeric',
                    Rule::unique('inventory_items')->where(function ($query) use ($request) {
                        $query->where('is_deleted', '=', 0)
                            ->where('heat_no', '=', $request->heat_no)
                            ->where('length', '=', $request->length);
                    })
                ],
            ],
            [
                'quantity.required' => "Quantity field is required.",
                'quantity.numeric' => "Quantity field must be a number.",
                'width.numeric' => "Width field must be a number.",
                'weight_received.numeric' => "Weight Received field must be a number.",
                //'warehouse.required' => "Warehouse field is required.",
                'heat_no.required' => "Heat Number field is required.",
                'length.numeric' => "Length field must be a number.",
                'length.unique' => "Length value has a same value with a same Heat Number.",
            ]
        ];

        if (Str::contains($request->item_type, 'PLATE')) {
            if ($request->is_excess) {
                $rules = [
                    [
                        'warehouse' => 'required',
                        'heat_no' => 'required',
                        'weight' => [
                            'required',
                            'numeric',
                            Rule::unique('inventory_items')->where(function ($query) use ($request) {
                                $query->where('is_deleted', '=', 0)
                                    ->where('heat_no', '=', $request->heat_no)
                                    ->where('weight', '=', $request->weight);
                            })
                        ],
                    ],
                    [
                        'weight.required' => "Weight field is required.",
                        'weight.numeric' => "Weight field must be a number.",
                        'warehouse.required' => "Warehouse field is required.",
                        'heat_no.required' => "Heat Number field is required.",
                    ]
                ];
            }

            $rules = [
                [
                    'quantity' => 'required|numeric',
                    'weight_received' => 'numeric',
                    'warehouse' => 'required',
                    'heat_no' => 'required',
                    'width' => [
                        'numeric',
                        Rule::unique('inventory_items')->where(function ($query) use ($request) {
                            $query->where('is_deleted', '=', 0)
                                ->where('heat_no', '=', $request->heat_no)
                                ->where('width', '=', $request->width);
                        })
                    ],
                    'length' => [
                        'numeric',
                        Rule::unique('inventory_items')->where(function ($query) use ($request) {
                            $query->where('is_deleted', '=', 0)
                                ->where('heat_no', '=', $request->heat_no)
                                ->where('length', '=', $request->length);
                        })
                    ],
                ],
                [
                    'quantity.required' => "Quantity field is required.",
                    'quantity.numeric' => "Quantity field must be a number.",
                    'weight_received.numeric' => "Weight Received field must be a number.",
                    'warehouse.required' => "Warehouse field is required.",
                    'heat_no.required' => "Heat Number field is required.",
                    'width.numeric' => "Width field must be a number.",
                    'width.unique' => "Width value has a same value with a same Heat Number.",
                    'length.numeric' => "Length field must be a number.",
                    'length.unique' => "Length value has a same value with a same Heat Number.",
                ]
            ];
        }

        return $rules;
    }

    public function show($id)
    {
        try {
            $data = InventoryItem::join('items', 'inventory_items.item_id', '=', 'items.id')
                ->where('inventory_items.is_deleted', '=', 0)
                ->where('inventory_items.id', '=', $id)
                ->select([
                    "inventory_items.*",
                    DB::raw("items.item_category as item_category"),
                    DB::raw("items.item_type as item_type"),
                    DB::raw("items.item_code as item_code"),
                    DB::raw("items.id as item_id"),
                    DB::raw("items.item_desc as item_desc")
                ])->first();

            return json_encode($data);
        } catch (\Throwable $th) {
            return response([
                'message' => $th->getMessage(),
                'status' => "error"
            ]);
        }
    }

    public function update($id, Request $request)
    {
        $rules = [
            'quantity' => 'required|numeric',
            'length' => 'numeric',
            'width' => 'numeric',
            'weight_received' => 'numeric',
            'warehouse' => 'required',
            'heat_no' => 'required'

        ];

        $this->validate($request, $rules);

        try {
            DB::beginTransaction();

            $inv = InventoryItem::find($id);

            $inv->item_id = $request->item_id;
            $inv->warehouse = $request->warehouse;
            $inv->quantity = $request->quantity;
            $inv->weight = $request->weight;
            $inv->length = $request->length;
            $inv->width = $request->width;
            $inv->heat_no = $request->heat_no;
            $inv->lot_no = $request->lot_no;
            $inv->sc_no = $request->sc_no;
            $inv->supplier = $request->supplier;
            $inv->supplier_heat_no = $request->supplier_heat_no;
            $inv->material_used = $request->material_used;
            $inv->weight_received = $request->weight_received;
            $inv->is_excess = ((bool)$request->is_excess == false) ? 0 : 1;

            $inv->update_user = $request->user_id;

            if ($inv->update()) {
                DB::commit();
                return response([
                    'message' => "Inventory Item details was successfully updated.",
                    'status' => "success",
                    'data' => $request->all()
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

    public function destroy(Request $request)
    {
        try {
            DB::beginTransaction();

            $delete = InventoryItem::whereIn('id', $request->id)->update([
                'is_deleted' => 1,
                'update_user' => $request->user_id,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            if ($delete) {
                DB::commit();
                return response([
                    'message' => "Inventory Item details was successfully deleted.",
                    'status' => "success",
                    'data' => $this->inventory_items()
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
            'message' => "Deleting Inventory Item details was unsuccessful.",
            'status' => "warning"
        ]);
    }
}
