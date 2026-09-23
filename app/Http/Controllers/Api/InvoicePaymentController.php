<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException as ExceptionApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\InvoicePayment;
use App\Models\InvoiceStatusHistory;
use App\Models\Invoice;
use App\Models\Notification;
use Illuminate\Http\Request;

class InvoicePaymentController extends Controller
{

    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'invoice_id' => 'required|integer|exists:invoices,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'nullable|in:cash,card,bank_transfer,upi,cheque',
                'transaction_id' => 'nullable|string|max:255',
                'payment_date' => 'required|date',
                'status' => 'nullable|in:pending,completed,failed,refunded',
                'notes' => 'nullable|string',
            ]);

            $invoice = Invoice::find($request->post('invoice_id'));

            if (!$invoice) {
                throw new ExceptionApiStatusZeroException('invoice not found');
            }

            $paidAmount = InvoicePayment::where('invoice_id', $invoice->id)
                ->where('status', 'completed')
                ->sum('amount');

            $remainingAmount = $invoice->total_amount - $paidAmount;

            if ($request->post('amount') > $remainingAmount) {
                throw new ExceptionApiStatusZeroException(
                    'payment amount cannot be greater than remaining invoice amount'
                );
            }

            $payment = InvoicePayment::create([
                'invoice_id' => $invoice->id,
                'amount' => $request->post('amount'),
                'payment_method' => $request->post('payment_method', 'cash'),
                'transaction_id' => $request->post('transaction_id'),
                'payment_date' => $request->post('payment_date'),
                'status' => $request->post('status', 'pending'),
                'notes' => $request->post('notes'),
            ]);

            if ($payment->status === 'completed') {

                $totalPaid = InvoicePayment::where('invoice_id', $invoice->id)
                    ->where('status', 'completed')
                    ->sum('amount');

                if ($totalPaid >= $invoice->total_amount) {
                    $invoice->status = 'paid';
                    $invoice->save();

                    InvoiceStatusHistory::create([
                        'invoice_id' => $invoice->id,
                        'status' => 'paid',
                        'comment' => 'Invoice fully paid',
                    ]);
                }
                if ($invoice->assigned_to) {
                    Notification::create([
                        'user_id' => $invoice->assigned_to,
                        'title' => 'Payment Received',
                        'message' => 'A payment has been received for an invoice assigned to you.',
                    ]);
                }
            }

            $this->response['msg'] = 'invoice payment saved successfully';
            $this->response['data'] = $payment;

            return response()->json($this->response);
        });
    }


    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'invoice_id' => 'nullable|integer|exists:invoices,id',
                'payment_method' => 'nullable|in:cash,card,bank_transfer,upi,cheque',
                'status' => 'nullable|in:pending,completed,failed,refunded',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $query = InvoicePayment::with('invoice');

            if ($request->post('invoice_id')) {
                $query->where(
                    'invoice_id',
                    $request->post('invoice_id')
                );
            }

            if ($request->post('payment_method')) {
                $query->where(
                    'payment_method',
                    $request->post('payment_method')
                );
            }

            if ($request->post('status')) {
                $query->where(
                    'status',
                    $request->post('status')
                );
            }

            $payments = $query
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'invoice payment list';
            $this->response['data'] = $payments;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:invoice_payments,id',
            ]);

            $payment = InvoicePayment::with('invoice')
                ->find($request->post('id'));

            if (!$payment) {
                throw new ExceptionApiStatusZeroException(
                    'invoice payment not found'
                );
            }

            $this->response['msg'] = 'invoice payment detail';
            $this->response['data'] = $payment;

            return response()->json($this->response);
        });
    }

    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:invoice_payments,id',
                'amount' => 'required|numeric|min:0.01',
                'payment_method' => 'nullable|in:cash,card,bank_transfer,upi,cheque',
                'transaction_id' => 'nullable|string|max:255',
                'payment_date' => 'required|date',
                'status' => 'nullable|in:pending,completed,failed,refunded',
                'notes' => 'nullable|string',
            ]);

            $payment = InvoicePayment::find($request->post('id'));

            if (!$payment) {
                throw new ExceptionApiStatusZeroException(
                    'invoice payment not found'
                );
            }

            $invoice = Invoice::find($payment->invoice_id);

            if (!$invoice) {
                throw new ExceptionApiStatusZeroException('invoice not found');
            }

            $completedAmount = InvoicePayment::where('invoice_id', $invoice->id)
                ->where('status', 'completed')
                ->where('id', '!=', $payment->id)
                ->sum('amount');

            if (
                $request->post('status', 'pending') === 'completed' &&
                $completedAmount + $request->post('amount') > $invoice->total_amount
            ) {
                throw new ExceptionApiStatusZeroException(
                    'payment amount cannot be greater than invoice amount'
                );
            }

            $payment->amount = $request->post('amount');
            $payment->payment_method = $request->post(
                'payment_method',
                'cash'
            );
            $payment->transaction_id = $request->post('transaction_id');
            $payment->payment_date = $request->post('payment_date');
            $payment->status = $request->post('status', 'pending');
            $payment->notes = $request->post('notes');

            $payment->save();

            if ($payment->status === 'completed') {

                $totalPaid = InvoicePayment::where('invoice_id', $invoice->id)
                    ->where('status', 'completed')
                    ->sum('amount');

                if ($totalPaid >= $invoice->total_amount) {
                    $invoice->status = 'paid';
                    $invoice->save();

                    InvoiceStatusHistory::create([
                        'invoice_id' => $invoice->id,
                        'status' => 'paid',
                        'comment' => 'Invoice fully paid',
                    ]);
                }
            }

            $this->response['msg'] = 'invoice payment updated successfully';
            $this->response['data'] = $payment;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:invoicepayment,id',
            ]);

            $payment = InvoicePayment::find($request->post('id'));

            if (!$payment) {
                throw new ExceptionApiStatusZeroException(
                    'invoice payment not found'
                );
            }

            $payment->delete();

            $this->response['msg'] = 'invoice payment delete successfully';

            return response()->json($this->response);
        });
    }

    public function summary(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'invoice_id' => 'required|integer|exists:invoices,id',
            ]);

            $invoice = Invoice::find($request->post('invoice_id'));

            if (!$invoice) {
                throw new ExceptionApiStatusZeroException('invoice not found');
            }

            $paidAmount = InvoicePayment::where('invoice_id', $invoice->id)
                ->where('status', 'completed')
                ->sum('amount');

            $remainingAmount = $invoice->total_amount - $paidAmount;

            $this->response['msg'] = 'invoice payment summary';
            $this->response['data'] = [
                'invoice_id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'total_amount' => $invoice->total_amount,
                'paid_amount' => $paidAmount,
                'remaining_amount' => max($remainingAmount, 0),
                'status' => $invoice->status,
            ];

            return response()->json($this->response);
        });
    }
}
