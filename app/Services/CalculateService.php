<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Receipt;
use App\Models\Quotation;
use App\Models\InvoiceDetail;
use App\Models\PurchaseOrder;
use App\Models\ReceiptDetail;
use App\Models\PurchaseDetail;
use App\Models\QuotationDetail;

class CalculateService
{

    /** Calculate Total */
    public function calculateTotal($request, $sumSubTotal, $id)
    {
        /** Calculate */
        $calculateTax = $sumSubTotal * $request['tax'] / 100;
        $calculateDiscount = $sumSubTotal * $request['discount'] / 100;
        $sumTotal = ($sumSubTotal - $calculateDiscount) + $calculateTax;

        /** Update Total Quotation */
        $addQuotation = Quotation::find($id);
        $addQuotation->sub_total = $sumSubTotal;
        $addQuotation->total = $sumTotal;
        $addQuotation->save();
    }

    public function calculateTotal_ByEdit($request)
    {
        /** Calculate */
        $sumSubTotalPrice = QuotationDetail::where('quotation_id', $request['id'])->get()->sum('total');
        $calculateTax = $sumSubTotalPrice * $request['tax'] / 100;
        $calculateDiscount = $sumSubTotalPrice * $request['discount'] / 100;
        $sumTotal = ($sumSubTotalPrice - $calculateDiscount) + $calculateTax;

        /** Update Total Quotation */
        $editQuotation = Quotation::find($request['id']);
        $editQuotation->sub_total = $sumSubTotalPrice;
        $editQuotation->total = $sumTotal;
        $editQuotation->save();
    }

    public function calculateTotalInvoice($request, $sumSubTotal, $id)
    {
         /** Calculate */
         $calculateTax = $sumSubTotal * $request['tax'] / 100;
         $calculateDiscount = $sumSubTotal * $request['discount'] / 100;
         $sumTotal = ($sumSubTotal - $calculateDiscount) + $calculateTax;

         /** Update Total Invoice */
         $addInvoice = Invoice::find($id);
         $addInvoice->sub_total = $sumSubTotal;
         $addInvoice->total = $sumTotal;
         $addInvoice->save();
    }

    public function calculateTotalInvoice_ByEdit($request)
    {
         /** Calculate */
         $sumSubTotalPrice = InvoiceDetail::where('invoice_id', $request['id'])->get()->sum('total');
         $calculateTax = $sumSubTotalPrice * $request['tax'] / 100;
         $calculateDiscount = $sumSubTotalPrice * $request['discount'] / 100;
         $sumTotal = ($sumSubTotalPrice - $calculateDiscount) + $calculateTax;

         /** Update Total Invoice */
         $editInvoice = Invoice::find($request['id']);
         $editInvoice->sub_total = $sumSubTotalPrice;
         $editInvoice->total = $sumTotal;
         $editInvoice->save();
    }

    public function calculateTotalReceipt($request, $sumSubTotal, $id)
    {
         /** Calculate */
         $calculateTax = $sumSubTotal * $request['tax'] / 100;
         $calculateDiscount = $sumSubTotal * $request['discount'] / 100;
         $sumTotal = ($sumSubTotal - $calculateDiscount) + $calculateTax;

         /** Update Total Receipt */
         $addReceipt = Receipt::find($id);
         $addReceipt->sub_total = $sumSubTotal;
         $addReceipt->total = $sumTotal;
         $addReceipt->save();
    }

    public function calculateTotalReceipt_ByEdit($request)
    {
         /** Calculate */
         $sumSubTotalPrice = ReceiptDetail::where('Receipt_id', $request['id'])->get()->sum('total');
         $calculateTax = $sumSubTotalPrice * $request['tax'] / 100;
         $calculateDiscount = $sumSubTotalPrice * $request['discount'] / 100;
         $sumTotal = ($sumSubTotalPrice - $calculateDiscount) + $calculateTax;

         /** Update Total Receipt */
         $editReceipt = Receipt::find($request['id']);
         $editReceipt->sub_total = $sumSubTotalPrice;
         $editReceipt->total = $sumTotal;
         $editReceipt->save();
    }

    public function calculateTotalPurchase($request, $sumSubTotal, $id)
    {
         /** Calculate */
         $calculateTax = $sumSubTotal * $request['tax'] / 100;
         $calculateDiscount = $sumSubTotal * $request['discount'] / 100;
         $sumTotal = ($sumSubTotal - $calculateDiscount) + $calculateTax;

         /** Update Total Purchase */
         $addPurchaseOrder = PurchaseOrder::find($id);
         $addPurchaseOrder->sub_total = $sumSubTotal;
         $addPurchaseOrder->total = $sumTotal;
         $addPurchaseOrder->save();
    }

    public function calculateTotalPurchase_ByEdit($request)
    {
         /** Calculate */
         $sumSubTotalPrice = PurchaseDetail::where('purchase_id', $request['id'])->get()->sum('total');
         $calculateTax = $sumSubTotalPrice * $request['tax'] / 100;
         $calculateDiscount = $sumSubTotalPrice * $request['discount'] / 100;
         $sumTotal = ($sumSubTotalPrice - $calculateDiscount) + $calculateTax;

         /** Update Total PurchaseOrder */
         $editPurchaseOrder = PurchaseOrder::find($request['id']);
         $editPurchaseOrder->sub_total = $sumSubTotalPrice;
         $editPurchaseOrder->total = $sumTotal;
         $editPurchaseOrder->save();
    }
}
