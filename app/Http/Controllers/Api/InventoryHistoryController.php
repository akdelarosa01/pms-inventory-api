<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Throwable;

class InventoryHistoryController extends Controller
{
    public function index(Request $request)
    {
        $histories = $this->history_list($request);

        return json_encode($histories);
    }

    private function history_list($request)
    {
        $histories =  DB::table("inventory_histories as h")
                        ->leftJoin('inventory_items as ii','h.inv_id','=','ii.id')
                        ->leftJoin('items as i','ii.item_id','=','i.id')
                        ->leftJoin('users as u','h.create_user','=','u.id')
                        ->select(
                            DB::raw("i.item_code"),
                            DB::raw("i.item_desc"),
                            DB::raw("i.item_category"),
                            DB::raw("h.transaction_type as transaction_type"),
                            DB::raw("h.remarks as remarks"),
                            
                            DB::raw("ii.warehouse as warehouse"),
                            DB::raw("ii.quantity as quantity"),
                            DB::raw("ii.weight as weight"),
                            DB::raw("ii.length as length"),
                            DB::raw("ii.width as width"),
                            DB::raw("u.firstname as firstname"),
                            DB::raw("h.updated_at as updated_at")
                        )->get();

        return $histories;
    }
}
