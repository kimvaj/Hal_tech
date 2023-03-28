<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ReceiptService;
use App\Http\Requests\ReceiptRequest;

class ReceiptsController extends Controller
{

    public $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function addReceipt(ReceiptRequest $request)
    {
        return $this->receiptService->addReceipt($request);
    }

    public function deleteReceipt(ReceiptRequest $request)
    {
        return $this->receiptService->deleteReceipt($request);
    }

    public function deleteReceiptDetail(ReceiptRequest $request)
    {
        return $this->receiptService->deleteReceiptDetail($request);
    }

    public function editReceipt(ReceiptRequest $request)
    {
        return $this->receiptService->editReceipt($request);
    }

    public function editReceiptDetail(ReceiptRequest $request)
    {
        return $this->receiptService->editReceiptDetail($request);
    }

    public function addReceiptDetail(ReceiptRequest $request)
    {
        return $this->receiptService->addReceiptDetail($request);
    }

    public function listReceipts(Request $request)
    {
        return $this->receiptService->listReceipts($request);
    }

    public function listReceiptDetails($id)
    {
        return $this->receiptService->listReceiptDetails($id);
    }

}
