<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Requests\CurrencyRequest;

class CurrencyController extends Controller
{
    public function addCurrency(CurrencyRequest $request)
    {

        $addCurrency = new Currency();
        $addCurrency->name = $request['name'];
        $addCurrency->short_name = $request['short_name'];
        $addCurrency->save();

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function editCurrency(CurrencyRequest $request)
    {
        $editCurrency = Currency::find($request['id']);
        $editCurrency->name = $request['name'];
        $editCurrency->short_name = $request['short_name'];
        $editCurrency->save();

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function deleteCurrency(CurrencyRequest $request)
    {
        $editCurrency = Currency::find($request['id']);
        $editCurrency->delete();

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function listCurrencies()
    {
        $items = Currency::orderBy('id', 'desc')->get();

        return response()->json([
            'currencies' => $items
        ]);
    }
}
