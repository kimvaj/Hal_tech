<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Quotation;
use App\Helpers\appHelper;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use App\Models\QuotationDetail;
use App\Services\CalculateService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\QuotationRequest;

class QuotationController extends Controller
{
    use ResponseAPI;

    public $calculateService;

    public function __construct(CalculateService $calculateService)
    {
        $this->calculateService = $calculateService;
    }



    public function listQuotations(Request $request)
    {
        $items = Quotation::orderBy('id', 'desc')->get();
        $items->transform(function ($item) {
            return $item->format();
        });

        return $this->success($items, 200);


        /*$items->map(function ($item) {
            $item['countDetail'] = QuotationDetail::where('quotation_id', $item['id'])->count();
            $item['company'] = Company::where('id', $item['company_id'])->first();
            $item['currency'] = Company::where('id', $item['currency_id'])->first();
            $item['user'] = User::where('id', $item['created_by'])->first();
        })*/

        return response()->json([
            'listQuotations' => $items,
        ]);
    }

    public function listQuotationDetails($id)
    {
        $item = Quotation::orderBy('id', 'desc')->where('id', $id)->first();
        $item['countDetail'] = QuotationDetail::where('quotation_id', $item['id'])->count();
        $item['company'] = Company::where('id', $item['company_id'])->first();
        $item['currency'] = Company::where('id', $item['currency_id'])->first();
        $item['user'] = User::where('id', $item['created_by'])->first();

        /**Detail */
        $details = QuotationDetail::where('quotation_id', $id)->get();


        return response()->json([
            'listQuotation' => $item,
            'details' => $details,
        ]);
    }

    public function addQuotation(QuotationRequest $request)
    {
        
        DB::beginTransaction();

        $addQuotation = new Quotation();
        $addQuotation->quotation_number = appHelper::generateQuotationNumber('QT-', 6);
        $addQuotation->quotation_name = $request['quotation_name'];
        $addQuotation->start_date = $request['start_date'];
        $addQuotation->end_date = $request['end_date'];
        $addQuotation->note = $request['note'];
        $addQuotation->company_id = $request['company_id'];
        $addQuotation->currency_id = $request['currency_id'];
        $addQuotation->discount = $request['discount'];
        $addQuotation->tax = $request['tax'];
        $addQuotation->created_by = Auth::user('api')->id;
        $addQuotation->save();


        $sumSubTotal = 0;
        /**Save Quotation Detail */
        if (!empty($request['quotation_details'])) {
            foreach ($request['quotation_details'] as $item) {
                $addDetail = new QuotationDetail();
                $addDetail->order = $item['order'];
                $addDetail->quotation_id = $addQuotation['id'];
                $addDetail->name = $item['name'];
                $addDetail->description = $item['description'];
                $addDetail->qty = $item['qty'];
                $addDetail->price = $item['price'];
                $addDetail->total = $item['qty'] * $item['price'];
                $addDetail->save();

                $sumSubTotal += $item['qty'] * $item['price'];
            }
        }

        /**Calculate */
        $this->calculateService->calculateTotal($request, $sumSubTotal, $addQuotation['id']);

        DB::commit();

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function editQuotation(QuotationRequest $request)
    {
        $editQuotation = Quotation::find($request['id']);
        $editQuotation->quotation_name = $request['quotation_name'];
        $editQuotation->start_date = $request['start_date'];
        $editQuotation->end_date = $request['end_date'];
        $editQuotation->note = $request['note'];
        $editQuotation->company_id = $request['company_id'];
        $editQuotation->currency_id = $request['currency_id'];
        $editQuotation->discount = $request['discount'];
        $editQuotation->tax = $request['tax'];
        $editQuotation->created_by = Auth::user('api')->id;
        $editQuotation->save();

        /**Update Calculate */
        $this->calculateService->calculateTotal_ByEdit($request);

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function editQuotationDetail(QuotationRequest $request)
    {
        $editDetail = QuotationDetail::find($request['id']);
        $editDetail->name = $request['name'];
        $editDetail->description = $request['description'];
        $editDetail->qty = $request['qty'];
        $editDetail->price = $request['price'];
        $editDetail->total = $request['qty'] * $request['price'];
        $editDetail->save();

        /**Update Quotation */
        $editQuotation = Quotation::find($editDetail['quotation_id']);

        /**Update Calculate */
        $this->calculateService->calculateTotal_ByEdit($editQuotation);

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function addQuotationDetail(QuotationRequest $request)
    {
        $addDetail = new QuotationDetail();
        $addDetail->order = $request['order'];
        $addDetail->quotation_id = $request['id'];
        $addDetail->name = $request['name'];
        $addDetail->description = $request['description'];
        $addDetail->qty = $request['qty'];
        $addDetail->price = $request['price'];
        $addDetail->total = $request['qty'] * $request['price'];
        $addDetail->save();

        /**Update Quotation */
        $editQuotation = Quotation::find($request['id']);

        /**Update Calculate */
        $this->calculateService->calculateTotal_ByEdit($editQuotation);

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function deleteQuotationDetail(QuotationRequest $request)
    {
        $deleteDetail = QuotationDetail::find($request['id']);
        $deleteDetail->delete();

        /**Update Quotation */
        $editQuotation = Quotation::find($deleteDetail['quotation_id']);

        /**Update Calculate */
        $this->calculateService->calculateTotal_ByEdit($editQuotation);

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function deleteQuotation(QuotationRequest $request)
    {
        $deleteDetail = Quotation::find($request['id']);
        $deleteDetail->delete();

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }
}
