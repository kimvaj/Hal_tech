<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Currency;
use App\Helpers\appHelper;
use App\Traits\ResponseAPI;
use Illuminate\Http\Request;
use App\Models\InvoiceDetail;
use App\Services\CalculateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\InvoiceRequest;

class InvoiceController extends Controller
{
    use ResponseAPI;

    public $calculateService;

    public function __construct(CalculateService $calculateService)
    {
        $this->calculateService = $calculateService;
    }

    public function listInvoices(Request $request)
    {

        $invoices = Invoice::select(
            'invoices.*'
        )


        ->orderBy('invoices.id', 'desc')

        ->get();
        $invoices->transform(function($item) {
            $sumSubTotal = InvoiceDetail::where('invoice_id', $item['id'])->select(DB::raw("IFNULL(sum(invoice_details.total), 0) as total"))->first()->total;
            $calculateTax = $sumSubTotal * $item['tax'] / 100;
            $calculateDiscount = $sumSubTotal * $item['discount'] / 100;
            $sumTotal = ($sumSubTotal - $calculateDiscount) + $calculateTax;

            //Merge Data Or Push Data =>( create new columns)
            $item['sub_total1'] = $sumSubTotal;
            $item['total1'] = $sumTotal;
            $item['countDetail'] = InvoiceDetail::where('invoice_id', $item['id'])
            ->select(DB::raw("IFNULL(count(invoice_details.total), 0) as total"))->first()->total;

            $item['countWhereIn'] = InvoiceDetail::where('invoice_id', $item['id'])
            ->whereIn('order', [1,2])
            ->select(DB::raw("IFNULL(count(invoice_details.total), 0) as total"))->first()->total;

            // $item['countStatus'] = Invoice::find($item['id'])
            // ->where(function($q){
            //     $q->where('status', '=', 'paid');
            // })
            // ->select(DB::raw("IFNULL(count(invoices.status), 0) as status"))->first()->status;

            // $item['countDetailPhou'] = InvoiceDetail::where('invoice_id', $item['id'])
            // ->where(function($q) {
                // $q->where('status', '=', 'paid');
                // $q->where('total', '>', 40000);
                // $q->OrWhere('total', '<', 60000);
                // $q->where('order', '!=', 3);

            //})

            // ->select(DB::raw("IFNULL(count(invoice_details.total), 0) as total"))->first()->total;
            // dd('dd');
             return $item;
        });

        return $invoices;
        /*$items = Invoice::orderBy('id', 'desc')->get();
        $items->transform(function($item) {
            return $item->format();
        });

        return $this->success($items, 200);

        return response()->json([
            'listInvoices' => $items,
        ]);*/
    }

    public function listInvoiceDetails($id)
    {

        $item = Invoice::orderBy('id', 'desc')->where('id', $id)->first();
        $item['countDetail'] = InvoiceDetail::where('invoice_id', $item['id'])->count();
        $item['company'] = Company::where('id', $item['company_id'])->first();
        $item['currency'] = Currency::where('id', $item['currency_id'])->first();
        $item['user'] = User::where('id', $item['created_by'])->first();

        /**$Detail */
        $details = InvoiceDetail::where('invoice_id', $id)->get();


        return response()->json([
            'listInvoices' => $item,
            'details' => $details
        ]);
    }

    public function addInvoice(InvoiceRequest $request)
    {

        DB::beginTransaction();

        $addInvoice = new Invoice();
        $addInvoice->invoice_number = appHelper::generateInvoiceNumber('QP-', 6);
        $addInvoice->invoice_name = $request['invoice_name'];
        $addInvoice->start_date = $request['start_date'];
        $addInvoice->end_date = $request['end_date'];
        $addInvoice->note = $request['note'];
        $addInvoice->company_id = $request['company_id'];
        $addInvoice->currency_id = $request['currency_id'];
        $addInvoice->discount = $request['discount'];
        $addInvoice->tax = $request['tax'];
        $addInvoice->created_by = Auth::user('api')->id;
        $addInvoice->save();


        $sumSubTotal = 0;
        /**Save Invoice Detail */
        if (!empty($request['invoice_details'])) {
            foreach ($request['invoice_details'] as $item) {
                $addDetail = new InvoiceDetail();
                $addDetail->order = $item['order'];
                $addDetail->invoice_id = $addInvoice['id'];
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
        $this->calculateService->calculateTotalInvoice($request, $sumSubTotal, $addInvoice['id']);

        DB::commit();

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function editInvoice(InvoiceRequest $request)
    {
        $addInvoice = Invoice::find($request['id']);
        $addInvoice->invoice_name = $request['invoice_name'];
        $addInvoice->start_date = $request['start_date'];
        $addInvoice->end_date = $request['end_date'];
        $addInvoice->note = $request['note'];
        $addInvoice->company_id = $request['company_id'];
        $addInvoice->currency_id = $request['currency_id'];
        $addInvoice->discount = $request['discount'];
        $addInvoice->tax = $request['tax'];
        $addInvoice->created_by = Auth::user('api')->id;
        $addInvoice->save();

        /**Update Calculate */
        $this->calculateService->calculateTotalInvoice_ByEdit($request);

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function editInvoiceDetail(InvoiceRequest $request)
    {
        $editDetail = InvoiceDetail::find($request['id']);
        $editDetail->name = $request['name'];
        $editDetail->description = $request['description'];
        $editDetail->qty = $request['qty'];
        $editDetail->price = $request['price'];
        $editDetail->total = $request['qty'] * $request['price'];
        $editDetail->save();

        /**Update Invoice */
        $editInvoice = Invoice::find($editDetail['invoice_id']);

        /**Update Calculate */
        $this->calculateService->calculateTotalInvoice_ByEdit($editInvoice);

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }


    public function addInvoiceDetail(InvoiceRequest $request)
    {
        $addDetail = new InvoiceDetail();
        $addDetail->order = $request['order'];
        $addDetail->invoice_id = $request['id'];
        $addDetail->name = $request['name'];
        $addDetail->description = $request['description'];
        $addDetail->qty = $request['qty'];
        $addDetail->price = $request['price'];
        $addDetail->total = $request['qty'] * $request['price'];
        $addDetail->save();

        /**Update Invoice */
        $editInvoice = Invoice::find($request['id']);

        /**Update Calculate */
        $this->calculateService->calculateTotalInvoice_ByEdit($editInvoice);


        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function deleteInvoiceDetail(InvoiceRequest $request)
    {
        $deleteDetail = InvoiceDetail::find($request['id']);
        $deleteDetail->delete();

        /**Update Invoice */
        $editInvoice = Invoice::find($deleteDetail['invoice_id']);

        /**Update calculate */
        $this->calculateService->calculateTotalInvoice_ByEdit($editInvoice);

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function deleteInvoice(InvoiceRequest $request)
    {
        $deleteDetail = Invoice::find($request['id']);
        $deleteDetail->delete();

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }

    public function updateInvoiceStatus(InvoiceRequest $request)
    {
        $updateStatus = Invoice::find($request['id']);
        $updateStatus->status = $request['status'];
        $updateStatus->save();

        return response()->json([
            'success' => true,
            'msg' => 'ສຳເລັດແລ້ວ'
        ]);
    }
}
