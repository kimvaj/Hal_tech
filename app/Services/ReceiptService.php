<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\Currency;
use App\Traits\ResponseAPI;
use App\Models\InvoiceDetail;
use App\Models\ReceiptDetail;
use App\Services\CalculateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ReceiptService
{
    use ResponseAPI;

    public $calculateService;

    public function __construct(CalculateService $calculateService)
    {
        $this->calculateService = $calculateService;
    }

    public function addReceipt($request)
    {

        $getInvoice = Invoice::find($request['invoice_id']);
        if (isset($getInvoice)) {
            // where
            // OrWhere
            // wherein
            // whereNotin
            // whereNull
            $getInvoiceDetails = InvoiceDetail::select(
                'invoice_details.*',
            )
            ->join(
                'invoices as invoice',
                'invoice.id',
                'invoice_details.invoice_id'
            )
                ->where('invoice_details.invoice_id', $getInvoice['id'])
                ->where('invoice.status', 'pending')
                ->get();



            if (count($getInvoiceDetails) > 0) {
                DB::beginTransaction();

                $addReceipt = new Receipt();
                $addReceipt->invoice_id = $getInvoice['id'];
                $addReceipt->company_id = $getInvoice['company_id'];
                $addReceipt->currency_id = $getInvoice['currency_id'];
                $addReceipt->created_by = Auth::user('api')->id;
                $addReceipt->receipt_name = $request['receipt_name'];
                $addReceipt->receipt_date = $request['receipt_date'];
                $addReceipt->sub_total = $getInvoice['sub_total'];
                $addReceipt->discount = $getInvoice['discount'];
                $addReceipt->tax = $getInvoice['tax'];
                $addReceipt->total = $getInvoice['total'];
                $addReceipt->note = $request['note'];
                $addReceipt->save();

                foreach ($getInvoiceDetails as $item) {
                    $addDetail = new ReceiptDetail();

                    $addDetail->receipt_id = $addReceipt['id'];
                    $addDetail->order = $item['order'];
                    $addDetail->name = $item['name'];
                    $addDetail->qty = $item['qty'];
                    $addDetail->price = $item['price'];
                    $addDetail->total = $item['total'];
                    $addDetail->description = $item['description'];
                    $addDetail->save();

                }

                DB::beginTransaction();

                return $this->success('ຜ່ານແລ້ວ', 200);

            }

            return $getInvoiceDetails;
        }

        return $this->error('ຜິດພາດ', 500);
    }

    public function deleteReceipt($request)
    {
        $deleteReceipt = Receipt::find($request['id']);
        $deleteReceipt->delete();


        return $this->success('ຜ່ານແລ້ວ', 200);
    }

    public function deleteReceiptDetail($request)
    {
        $deleteDetail = ReceiptDetail::find($request['id']);
        $deleteDetail->delete();

        //Update Receipt
        $editReceipt = Receipt::find($deleteDetail['receipt_id']);

        //Update Calculate Receipt
        $this->calculateService->calculateTotalReceipt_ByEdit($editReceipt);

        return $this->success('ຜ່ານແລ້ວ', 200);
    }

    public function editReceipt($request)
    {
        DB::beginTransaction();

            $addReceipt = Receipt::find($request['id']);
            $addReceipt->invoice_id = $request['invoice_id'];
            $addReceipt->company_id = $request['company_id'];
            $addReceipt->currency_id = $request['currency_id'];
            $addReceipt->created_by = Auth::user('api')->id;
            $addReceipt->receipt_name = $request['receipt_name'];
            $addReceipt->receipt_date = $request['receipt_date'];
            $addReceipt->discount = $request['discount'];
            $addReceipt->tax = $request['tax'];
            $addReceipt->note = $request['note'];
            $addReceipt->save();

            //Update calculate
            $this->calculateService->calculateTotalReceipt_ByEdit($request);

        DB::commit();

        return $this->success('ຜ່ານແລ້ວ', 200);
    }

    public function editReceiptDetail($request)
    {


        $editDetail = ReceiptDetail::find($request['id']);
        $editDetail->name = $request['name'];
        $editDetail->description = $request['description'];
        $editDetail->qty = $request['qty'];
        $editDetail->price = $request['price'];
        $editDetail->total = $request['qty'] * $request['price'];
        $editDetail->save();

         /**Update Receipt */
         $editReceipt = Receipt::find($editDetail['receipt_id']);

         /**Update Calculate */
         $this->calculateService->calculateTotalReceipt_ByEdit($editReceipt);

         return $this->success('ຜ່ານແລ້ວ', 200);
    }

    public function addReceiptDetail($request)
    {
        $addDetail = new ReceiptDetail();
        $addDetail->order = $request['order'];
        $addDetail->receipt_id = $request['id'];
        $addDetail->name = $request['name'];
        $addDetail->description = $request['description'];
        $addDetail->qty = $request['qty'];
        $addDetail->price = $request['price'];
        $addDetail->total = $request['qty'] * $request['price'];
        $addDetail->save();

        /**Update Receipt */
        $editReceipt = Receipt::find($request['id']);

        /**Update Calculate */
        $this->calculateService->calculateTotalReceipt_ByEdit($editReceipt);

        return $this->success('ຜ່ານແລ້ວ', 200);
    }

    public function listReceipts($request)
    {
        $receipts = Receipt::select(
            'receipts.*'
        )
        ->orderBy('receipts.id', 'desc')
        ->get();
        $receipts->transform(function($item) {
            $sumSubTotal = ReceiptDetail::where('receipt_id', $item['id'])->select(DB::raw("IFNULL(sum(receipt_details.total), 0) as total"))->first()->total;
            $calculateTax = $sumSubTotal * $item['tax'] / 100;
            $calculateDiscount = $sumSubTotal * $item['discount'] / 100;
            $sumTotal = ($sumSubTotal - $calculateDiscount) + $calculateTax;

            //Merge Data Or Push Data =>( create new columns)
            $item['sub_total75'] = $sumSubTotal;
            $item['total75'] = $sumTotal;
            $item['countDetail'] = ReceiptDetail::where('receipt_id', $item['id'])->select(DB::raw("IFNULL(count(receipt_details.total), 0) as total"))->first()->total;

            $item['countWhereIn'] = ReceiptDetail::where('receipt_id', $item['id'])
            ->whereIn('order', [1,2])
            ->select(DB::raw("IFNULL(count(receipt_details.total), 0) as total"))->first()->total;

            return $item;
        });

        return $receipts;
    }

    public function listReceiptDetails($id)
    {
        $item = Receipt::orderBy('id', 'desc')->where('id', $id)->first();
        $item['countDetail'] = ReceiptDetail::where('receipt_id', $item['id'])->count();
        $item['invoice'] = Invoice::where('id', $item['invoice_id'])->first();
        $item['company'] = Company::where('id', $item['company_id'])->first();
        $item['currency'] = Currency::where('id', $item['currency_id'])->first();
        $item['user'] = User::where('id', $item['create_by'])->first();

        $details = ReceiptDetail::where('receipt_id', $id)->get();

        return response()->json([
            'listReceipts' => $item,
            'details' => $details
        ]);

    }

}
