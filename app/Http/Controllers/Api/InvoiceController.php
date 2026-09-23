<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\InvoiceStatusHistory;
use App\Models\Notification;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{

    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'quotation_id' => 'nullable|integer|exists:quotations,id',
                'deal_id' => 'nullable|integer|exists:deals,id',
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'invoice_date' => 'required|date',
                'due_date' => 'nullable|date',
                'amount' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:draft,sent,paid,overdue,cancelled',
                'notes' => 'nullable|string',
            ]);

            if (
                !$request->post('quotation_id') &&
                !$request->post('deal_id') &&
                !$request->post('lead_id') &&
                !$request->post('customer_id')
            ) {
                throw new ApiStatusZeroException(
                    'quotation, deal, lead or customer is required'
                );
            }

            $amount = (float) $request->post('amount', 0);
            $taxAmount = (float) $request->post('tax_amount', 0);
            $discountAmount = (float) $request->post('discount_amount', 0);

            $totalAmount = $amount + $taxAmount - $discountAmount;

            if ($totalAmount < 0) {
                throw new ApiStatusZeroException(
                    'discount amount cannot be greater than total amount'
                );
            }

            $invoiceNumber = 'INV-' . date('YmdHis') . '-' . rand(100, 999);

            $invoice = Invoice::create([
                'quotation_id' => $request->post('quotation_id'),
                'deal_id' => $request->post('deal_id'),
                'lead_id' => $request->post('lead_id'),
                'customer_id' => $request->post('customer_id'),
                'assigned_to' => $request->post('assigned_to'),
                'invoice_number' => $invoiceNumber,
                'invoice_date' => $request->post('invoice_date'),
                'due_date' => $request->post('due_date'),
                'amount' => $amount,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'status' => $request->post('status', 'draft'),
                'notes' => $request->post('notes'),
            ]);

            InvoiceStatusHistory::create([
                'invoice_id' => $invoice->id,
                'status' => $invoice->status,
                'comment' => 'Invoice created',
            ]);

            if ($invoice->assigned_to) {
                Notification::create([
                    'user_id' => $invoice->assigned_to,
                    'title' => 'New Invoice Assigned',
                    'message' => 'A new invoice has been assigned to you.',
                ]);
            }

            $this->response['msg'] = 'invoice saved successfully';
            $this->response['data'] = $invoice;

            return response()->json($this->response);
        });
    }

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'quotation_id' => 'nullable|integer|exists:quotations,id',
                'deal_id' => 'nullable|integer|exists:deals,id',
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'status' => 'nullable|in:draft,sent,paid,overdue,cancelled',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $query = Invoice::with(
                'quotation',
                'deal',
                'lead',
                'customer',
                'assignedUser',
                'statusHistories',
                'payments'
            );

            if ($request->post('quotation_id')) {
                $query->where(
                    'quotation_id',
                    $request->post('quotation_id')
                );
            }

            if ($request->post('deal_id')) {
                $query->where('deal_id', $request->post('deal_id'));
            }

            if ($request->post('lead_id')) {
                $query->where('lead_id', $request->post('lead_id'));
            }

            if ($request->post('customer_id')) {
                $query->where(
                    'customer_id',
                    $request->post('customer_id')
                );
            }

            if ($request->post('assigned_to')) {
                $query->where(
                    'assigned_to',
                    $request->post('assigned_to')
                );
            }

            if ($request->post('status')) {
                $query->where('status', $request->post('status'));
            }

            $invoices = $query
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'invoice list';
            $this->response['data'] = $invoices;

            return response()->json($this->response);
        });
    }



    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:invoices,id',
            ]);

            $invoice = Invoice::with(
                'quotation',
                'deal',
                'lead',
                'customer',
                'assignedUser',
                'statusHistories',
                'payments'
            )->find($request->post('id'));

            if (!$invoice) {
                throw new ApiStatusZeroException('invoice not found');
            }


            $paidAmount = InvoicePayment::where('invoice_id', $invoice->id)
                ->where('status', 'completed')
                ->sum('amount');

            $remainingAmount = $invoice->total_amount - $paidAmount;

            $this->response['data'] = [
                'invoice' => $invoice,
                'paid_amount' => $paidAmount,
                'remaining_amount' => $remainingAmount,
            ];

            $this->response['msg'] = 'invoice detail';
            $this->response['data'] = $invoice;

            return response()->json($this->response);
        });
    }

    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:invoices,id',
                'quotation_id' => 'nullable|integer|exists:quotations,id',
                'deal_id' => 'nullable|integer|exists:deals,id',
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'invoice_date' => 'required|date',
                'due_date' => 'nullable|date',
                'amount' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'status' => 'nullable|in:draft,sent,paid,overdue,cancelled',
                'notes' => 'nullable|string',
            ]);

            if (
                !$request->post('quotation_id') &&
                !$request->post('deal_id') &&
                !$request->post('lead_id') &&
                !$request->post('customer_id')
            ) {
                throw new ApiStatusZeroException(
                    'quotation, deal, lead or customer is required'
                );
            }


            $invoice = Invoice::find($request->post('id'));

            if (!$invoice) {
                throw new ApiStatusZeroException('invoice not found');
            }

            $oldAssignedTo = $invoice->assigned_to;

            $amount = (float) $request->post('amount', 0);
            $taxAmount = (float) $request->post('tax_amount', 0);
            $discountAmount = (float) $request->post('discount_amount', 0);

            $totalAmount = $amount + $taxAmount - $discountAmount;

            $oldStatus = $invoice->status;
            if ($totalAmount < 0) {
                throw new ApiStatusZeroException(
                    'discount amount cannot be greater than total amount'
                );
            }

            $invoice->quotation_id = $request->post('quotation_id');
            $invoice->deal_id = $request->post('deal_id');
            $invoice->lead_id = $request->post('lead_id');
            $invoice->customer_id = $request->post('customer_id');
            $invoice->assigned_to = $request->post('assigned_to');
            $invoice->invoice_date = $request->post('invoice_date');
            $invoice->due_date = $request->post('due_date');
            $invoice->amount = $amount;
            $invoice->tax_amount = $taxAmount;
            $invoice->discount_amount = $discountAmount;
            $invoice->total_amount = $totalAmount;
            $invoice->status = $request->post('status', 'draft');
            $invoice->notes = $request->post('notes');

            $invoice->save();

            if ($invoice->status != $oldStatus) {
                InvoiceStatusHistory::create([
                    'invoice_id' => $invoice->id,
                    'status' => $invoice->status,
                    'comment' => 'Invoice status updated',
                ]);
            }

            if ($invoice->assigned_to && $invoice->assigned_to != $oldAssignedTo) {
                Notification::create([
                    'user_id' => $invoice->assigned_to,
                    'title' => 'Invoice Assigned',
                    'message' => 'A new invoice has been assigned to you.',
                ]);
            }

            $this->response['msg'] = 'invoice updated successfully';
            $this->response['data'] = $invoice;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:invoices,id',
            ]);

            $invoice = Invoice::find($request->post('id'));

            if (!$invoice) {
                throw new ApiStatusZeroException('invoice not found');
            }

            $invoice->delete();

            $this->response['msg'] = 'invoice deleted successfully';

            return response()->json($this->response);
        });
    }

    public function myInvoices(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'status' => 'nullable|in:draft,sent,paid,overdue,cancelled',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $query = Invoice::with(
                'quotation',
                'deal',
                'lead',
                'customer',
                'assignedUser'
            )->where('assigned_to', $request->user()->id);

            if ($request->post('status')) {
                $query->where('status', $request->post('status'));
            }

            $invoices = $query
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'my invoices';
            $this->response['data'] = $invoices;

            return response()->json($this->response);
        });
    }

    public function myInvoicesSummary(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $userId = $request->user()->id;

            $this->response['msg'] = 'my invoices summary';

            $this->response['data'] = [
                'total_invoices' => Invoice::where(
                    'assigned_to',
                    $userId
                )->count(),

                'draft_invoices' => Invoice::where(
                    'assigned_to',
                    $userId
                )->where('status', 'draft')->count(),

                'sent_invoices' => Invoice::where(
                    'assigned_to',
                    $userId
                )->where('status', 'sent')->count(),

                'paid_invoices' => Invoice::where(
                    'assigned_to',
                    $userId
                )->where('status', 'paid')->count(),

                'overdue_invoices' => Invoice::where(
                    'assigned_to',
                    $userId
                )->where('status', 'overdue')->count(),

                'cancelled_invoices' => Invoice::where(
                    'assigned_to',
                    $userId
                )->where('status', 'cancelled')->count(),

                'total_amount' => Invoice::where(
                    'assigned_to',
                    $userId
                )->sum('total_amount'),

                'paid_amount' => Invoice::where(
                    'assigned_to',
                    $userId
                )->where('status', 'paid')->sum('total_amount'),
            ];

            return response()->json($this->response);
        });
    }

    public function markOverdue(Request $request)
    {
        return handleApiRequest(function () {

            $invoices = Invoice::where('due_date', '<', now()->toDateString())
                ->whereNotIn('status', [
                    'paid',
                    'cancelled',
                    'overdue',
                ])
                ->get();

            $count = 0;

            foreach ($invoices as $invoice) {
                $invoice->status = 'overdue';
                $invoice->save();

                if ($invoice->assigned_to) {
                    Notification::create([
                        'user_id' => $invoice->assigned_to,
                        'title' => 'Invoice Overdue',
                        'message' => 'An invoice assigned to you is now overdue.',
                    ]);
                }

                InvoiceStatusHistory::create([
                    'invoice_id' => $invoice->id,
                    'status' => 'overdue',
                    'comment' => 'Invoice marked as overdue',
                ]);

                $count++;
            }

            $this->response['msg'] = 'overdue invoices updated successfully';
            $this->response['data'] = [
                'updated_count' => $count,
            ];

            return response()->json($this->response);
        });
    }
}
