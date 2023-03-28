<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\CurrencyController;
use App\Http\Controllers\ReceiptsController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\PurchaseOrderController;

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);

});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::group([
    'middleware' => [
        'auth.jwt',
        'set.locale'
    ],
    'role:superadmin|admin',
    'prefix' => 'admin',
], function() {

Route::post('add-currency', [CurrencyController::class, 'addCurrency'])->name('add.currency');
Route::put('edit-currency/{id}', [CurrencyController::class, 'editCurrency'])->name('edit.currency');
Route::delete('delete-currency/{id}', [CurrencyController::class, 'deleteCurrency'])->name('delete.currency');
Route::get('list-currencies', [CurrencyController::class, 'listCurrencies']);


Route::post('add-company', [CompanyController::class, 'addCompany'])->name('add.company');
Route::post('edit-company/{id}', [CompanyController::class, 'editCompany'])->name('edit.company');
Route::delete('delete-company/{id}', [CompanyController::class, 'deleteCompany'])->name('delete.company');
Route::get('list-companies', [CompanyController::class, 'listCompanies'])->name('list.companies');


Route::get('list-quotations', [QuotationController::class, 'listQuotations']);
Route::get('list-quotation-Detail/{id}', [QuotationController::class, 'listQuotationDetails']);

Route::post('add-quotation', [QuotationController::class, 'addQuotation']);
//Edit 1
Route::put('edit-quotation/{id}', [QuotationController::class, 'editQuotation'])->name('edit.quotation');
//Edit 2
Route::put('edit-quotation-detail/{id}', [QuotationController::class, 'editQuotationDetail'])->name('edit.quotation.detail');

Route::post('add-quotation-detail/{id}', [QuotationController::class, 'addQuotationDetail'])->name('add.quotation.detail');
//Delete 1
Route::delete('delete-quotation-detail/{id}', [QuotationController::class, 'deleteQuotationDetail'])->name('delete.quotation.detail');
//Delete 2
Route::delete('delete-quotation/{id}', [QuotationController::class, 'deleteQuotation'])->name('delete.quotation');

//List 1
Route::get('list-invoices', [InvoiceController::class, 'listInvoices']);
//List 2
Route::get('list-invoice-Detail/{id}', [InvoiceController::class, 'listInvoiceDetails']);

Route::post('add-invoice', [InvoiceController::class, 'addInvoice'])->name('add.invoice');
// Edit 1
Route::put('edit-invoice/{id}', [InvoiceController::class, 'editInvoice'])->name('edit.invoice');
//Edit 2
Route::put('edit-invoice-detail/{id}', [InvoiceController::class, 'editInvoiceDetail'])->name('edit.invoice.detail');
Route::post('add-invoice-detail/{id}', [InvoiceController::class, 'addInvoiceDetail'])->name('add.invoice.detail');
//Delete 1
Route::delete('delete-invoice-detail/{id}', [InvoiceController::class, 'deleteInvoiceDetail'])->name('delete.invoice.detail');
//Delete 2
Route::delete('delete-invoice/{id}', [InvoiceController::class, 'deleteInvoice'])->name('delete.invoice');
Route::put('update-invoice-status/{id}', [InvoiceController::class, 'updateInvoiceStatus'])->name('update.invoice.status');

Route::post('add-receipt', [ReceiptsController::class, 'addReceipt'])->name('add.receipt');
Route::delete('delete-receipt/{id}', [ReceiptsController::class, 'deleteReceipt'])->name('delete.receipt');
Route::delete('delete-receipt-detail/{id}', [ReceiptsController::class, 'deleteReceiptDetail'])->name('delete.receipt.detail');
Route::put('edit-receipt/{id}', [ReceiptsController::class, 'editReceipt'])->name('edit.receipt');
Route::put('edit-receipt-Detail/{id}', [ReceiptsController::class, 'editReceiptDetail'])->name('edit.receipt.detail');
Route::post('add-receipt-detail/{id}', [ReceiptsController::class, 'addReceiptDetail'])->name('add.receipt.detail');
Route::get('list-receipts', [ReceiptsController::class, 'listReceipts']);
Route::get('list-receipt-Detail/{id}', [ReceiptsController::class, 'listReceiptDetails']);

Route::post('add-purchaseOrder', [PurchaseOrderController::class, 'addPurchaseOrder'])->name('add.purchaseOrder');
Route::delete('delete-purchaseOrder/{id}', [PurchaseOrderController::class, 'deletePurchaseOrder'])->name('delete.purchaseOrder');
Route::delete('delete-purchaseDetail/{id}', [PurchaseOrderController::class, 'deletePurchaseDetail'])->name('delete.purchase.detail');
Route::put('edit-purchaseOrder/{id}', [PurchaseOrderController::class, 'editPurchaseOrder'])->name('edit.purchaseOrder');
Route::put('edit-purchaseDetail/{id}', [PurchaseOrderController::class, 'editPurchaseDetail'])->name('edit.purchase.detail');
Route::post('add-purchaseDetail/{id}', [PurchaseOrderController::class, 'addPurchaseDetail'])->name('add.purchase.detail');
Route::get('list-purchaseOrders', [PurchaseOrderController::class, 'listPurchaseOrders']);
Route::get('list-purchaseDetail/{id}', [PurchaseOrderController::class, 'listPurchaseDetails']);


});
