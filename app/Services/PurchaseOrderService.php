<?php

namespace App\Services;

use App\Models\User;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Currency;
use App\Traits\ResponseAPI;
use App\Models\InvoiceDetail;
use App\Models\PurchaseOrder;
use App\Models\purchaseDetail;
use App\Services\CalculateService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderService
{
    use ResponseAPI;

    public $calculateService;

    public function __construct(CalculateService $calculateService)
    {
        $this->calculateService = $calculateService;
    }

    public function addPurchaseOrder($request)
    {
        $getInvoice = Invoice::find($request['invoice_id']);
        if (isset($getInvoice)){
            //where
            //OrWhere
            //WhereIn
            //WhereNotIn
            //WhereNull
            $getInvoiceDetails = InvoiceDetail::select(
                'invoice_details.*',
            )
            ->join(
                'invoices as invoice',
                'invoice.id',
                'invoice_details.invoice_id'
            )
            ->where('invoice_details.invoice_id', $getInvoice['id'])
            ->get();

            if (count($getInvoiceDetails) > 0) {
                DB::beginTransaction();
                    $addPurchase = new PurchaseOrder();
                    $addPurchase->invoice_id = $getInvoice['id'];
                    $addPurchase->company_id = $getInvoice['company_id'];
                    $addPurchase->currency_id = $getInvoice['currency_id'];
                    $addPurchase->created_by = Auth::user('api')->id;
                    $addPurchase->purchase_name = $request['purchase_name'];
                    $addPurchase->date = $request['date'];
                    $addPurchase->note = $request['note'];
                    $addPurchase->total = $getInvoice['total'];
                    $addPurchase->sub_total = $getInvoice['sub_total'];
                    $addPurchase->discount = $getInvoice['discount'];
                    $addPurchase->tax = $getInvoice['tax'];
                    $addPurchase->save();

                    foreach ($getInvoiceDetails as $item) {
                        $addDetail = new purchaseDetail();
                        $addDetail->purchase_id = $addPurchase['id'];
                        $addDetail->order = $item['order'];
                        $addDetail->name = $item['name'];
                        $addDetail->qty = $item['qty'];
                        $addDetail->price = $item['price'];
                        $addDetail->total = $item['total'];
                        $addDetail->description = $item['description'];
                        $addDetail->save();


                    }

                DB::commit();

                return $this->success('ຜ່ານແລ້ວ', 200);

            }

            return $getInvoiceDetails;

        }

        return $this->error('ຜິດພາດ', 500);

    }

    public function deletePurchaseOrder($request)
    {
        $deletePurchase = PurchaseOrder::find($request['id']);
        $deletePurchase->delete();

        return $this->success('ຜ່ານແລ້ວ', 200);
    }

    public function deletePurchaseDetail($request)
    {
        $deleteDetail = PurchaseDetail::find($request['id']);
        $deleteDetail->delete();

        //Update PurchaseOrder
        $editPurchaseOrder = PurchaseOrder::find($deleteDetail['purchase_id']);

        //Update Calculate PurchaseOrder
        $this->calculateService->calculateTotalPurchase_ByEdit($editPurchaseOrder);

        return $this->success('ຜ່ານແລ້ວ', 200);
    }

    public function editPurchaseOrder($request)
    {
        DB::beginTransaction();

            $addPurchase = PurchaseOrder::find($request['id']);
            $addPurchase->invoice_id = $request['invoice_id'];
            $addPurchase->company_id = $request['company_id'];
            $addPurchase->created_by = Auth::user('api')->id;
            $addPurchase->currency_id = $request['currency_id'];
            $addPurchase->purchase_name = $request['purchase_name'];
            $addPurchase->date = $request['date'];
            $addPurchase->discount = $request['discount'];
            $addPurchase->tax = $request['tax'];
            $addPurchase->note = $request['note'];
            $addPurchase->save();

            // Update calculate
            $this->calculateService->calculateTotalPurchase_ByEdit($request);

        DB::commit();

        return $this->success('ຜ່ານແລ້ວ', 200);
    }

    public function editPurchaseDetail($request)
    {
        DB::beginTransaction();
            $editDetail = PurchaseDetail::find($request['id']);
            $editDetail->name = $request['name'];
            $editDetail->qty = $request['qty'];
            $editDetail->price = $request['price'];
            $editDetail->description = $request['description'];
            $editDetail->save();

            //Update PurchaseOrder
            $editPurchaseOrder = PurchaseOrder::find($editDetail['purchase_id']);

            //Update calculate
            $this->calculateService->calculateTotalPurchase_ByEdit($editPurchaseOrder);

        DB::commit();

        return $this->success('ຜ່ານແລ້ວ', 200);
    }

    public function addPurchaseDetail($request)
    {
        DB::beginTransaction();

            $addDetail = new PurchaseDetail();
            $addDetail->order = $request['order'];
            $addDetail->purchase_id = $request['id'];
            $addDetail->name = $request['name'];
            $addDetail->qty = $request['qty'];
            $addDetail->price = $request['price'];
            $addDetail->description = $request['description'];
            $addDetail->total = $request['qty'] * $request['price'];
            $addDetail->save();

            /**Update PurchaseOrder */
            $editPurchaseOrder = PurchaseOrder::find($request['id']);

            /**Update Calculate */
            $this->calculateService->calculateTotalPurchase_ByEdit($editPurchaseOrder);

        DB::commit();

        return $this->success('ຜ່ານແລ້ວ', 200);
    }

    public function listPurchaseOrders($request)
    {
        $purchaseOrders = PurchaseOrder::select(
            'purchase_Orders.*'
        )
        ->orderBy('purchase_Orders.id', 'desc')
        ->get();
        $purchaseOrders->transform(function($item) {
            $sumSubTotal = PurchaseDetail::where('purchase_id', $item['id'])->select(DB::raw("IFNULL(sum(purchase_details.total), 0) as total"))->first()->total;
            $calculateTax = $sumSubTotal * $item['tax'] / 100;
            $calculateDiscount = $sumSubTotal * $item['discount'] / 100;
            $sumTotal = ($sumSubTotal - $calculateDiscount) + $calculateTax;

            //Merge Data Or Push Data => ( create new columns )
            $item['sub_total200'] = $sumSubTotal;
            $item['total200'] = $sumTotal;
            $item['countDetail'] = PurchaseDetail::where('purchase_id', $item['id'])->select(DB::raw("IFNULL(count(purchase_details.total), 0) as total"))->first()->total;

            $item['countWhereIn'] = PurchaseDetail::where('purchase_id', $item['id'])
            ->whereIn('order', [1,2])
            ->select(DB::raw("IFNULL(count(purchase_details.total), 0) as total"))->first()->total;

            return $item;

        });
        return $purchaseOrders;
    }

    public function listPurchaseDetails($id)
    {
        $item = PurchaseOrder::orderBy('id', 'desc')->where('id', $id)->first();
        $item['countDetail'] = PurchaseDetail::where('purchase_id', $item['id'])->count();
        $item['invoice'] = Invoice::where('id', $item['invoice_id'])->first();
        $item['company'] = Company::where('id', $item['company_id'])->first();
        $item['currency'] = Currency::where('id', $item['currency_id'])->first();
        $item['user'] = User::where('id', $item['created_by'])->first();

        $details = PurchaseDetail::where('purchase_id', $id)->get();

        return response()->json([
            'listPurchaseDetails' => $item,
            'details' => $details
        ]);

    }

}




