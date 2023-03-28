<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PurchaseOrderService;
use App\Http\Requests\PurchaseOrderRequest;

class PurchaseOrderController extends Controller
{

    public $purchaseOrderService;

    public function __construct(PurchaseOrderService $purchaseOrderService)
    {
        $this->purchaseOrderService = $purchaseOrderService;
    }

    public function addPurchaseOrder(PurchaseOrderRequest $request)
    {
        return $this->purchaseOrderService->addPurchaseOrder($request);
    }

    public function deletePurchaseOrder(PurchaseOrderRequest $request)
    {
        return $this->purchaseOrderService->deletePurchaseOrder($request);
    }

    public function deletePurchaseDetail(PurchaseOrderRequest $request)
    {
        return $this->purchaseOrderService->deletePurchaseDetail($request);
    }

    public function editPurchaseOrder(PurchaseOrderRequest $request)
    {
        return $this->purchaseOrderService->editPurchaseOrder($request);
    }

    public function editPurchaseDetail(PurchaseOrderRequest $request)
    {
        return $this->purchaseOrderService->editPurchaseDetail($request);
    }

    public function addPurchaseDetail(PurchaseOrderRequest $request)
    {
        return $this->purchaseOrderService->addPurchaseDetail($request);
    }

    public function listPurchaseOrders(Request $request)
    {
        return $this->purchaseOrderService->listPurchaseOrders($request);
    }

    public function listPurchaseDetails( $id)
    {
        return $this->purchaseOrderService->listPurchaseDetails($id);
    }
}
