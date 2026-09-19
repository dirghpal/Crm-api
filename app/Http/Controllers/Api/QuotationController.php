<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Notification;
use App\Models\Quotation;
use App\Models\QuotationStatusHistory;

class QuotationController extends Controller
{

    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'deal_id' => 'nullable|integer|exists:deals,id',
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'valid_until' => 'nullable|date',
                'status' => 'nullable|in:draft,sent,accepted,rejected,expired',
                'notes' => 'nullable|string',
            ]);

            if (
                !$request->post('deal_id') &&
                !$request->post('lead_id') &&
                !$request->post('customer_id')
            ) {
                throw new ApiStatusZeroException(
                    'deal, lead or customer is required'
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

            $quotationNumber = 'QT-' . date('YmdHis') . '-' . rand(100, 999);

            $quotation = Quotation::create([
                'deal_id' => $request->post('deal_id'),
                'lead_id' => $request->post('lead_id'),
                'customer_id' => $request->post('customer_id'),
                'assigned_to' => $request->post('assigned_to'),
                'quotation_number' => $quotationNumber,
                'title' => $request->post('title'),
                'amount' => $amount,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'valid_until' => $request->post('valid_until'),
                'status' => $request->post('status', 'draft'),
                'notes' => $request->post('notes'),
            ]);

            QuotationStatusHistory::create([
                'quotation_id' => $quotation->id,
                'status' => $quotation->status,
                'comment' => 'Quotation created',
            ]);

            if ($quotation->assigned_to) {
                Notification::create([
                    'user_id' => $quotation->assigned_to,
                    'title' => 'New Quotation Assigned',
                    'message' => 'A new quotation has been assigned to you.',
                ]);
            }

            $this->response['msg'] = 'quotation saved successfully';
            $this->response['data'] = $quotation;

            return response()->json($this->response);
        });
    }

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'deal_id' => 'nullable|integer|exists:deals,id',
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'status' => 'nullable|in:draft,sent,accepted,rejected,expired',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $query = Quotation::with(
                'deal',
                'lead',
                'customer',
                'assignedUser'
            );

            if ($request->post('deal_id')) {
                $query->where('deal_id', $request->post('deal_id'));
            }

            if ($request->post('lead_id')) {
                $query->where('lead_id', $request->post('lead_id'));
            }

            if ($request->post('customer_id')) {
                $query->where('customer_id', $request->post('customer_id'));
            }

            if ($request->post('assigned_to')) {
                $query->where('assigned_to', $request->post('assigned_to'));
            }

            if ($request->post('status')) {
                $query->where('status', $request->post('status'));
            }

            $quotations = $query
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'quotation list';
            $this->response['data'] = $quotations;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:quotations,id',
            ]);

            $quotation = Quotation::with(
                'deal',
                'lead',
                'customer',
                'assignedUser',
                'statusHistories'
            )->find($request->post('id'));

            if (!$quotation) {
                throw new ApiStatusZeroException('quotation not found');
            }

            $this->response['msg'] = 'quotation detail';
            $this->response['data'] = $quotation;

            return response()->json($this->response);
        });
    }

    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:quotations,id',
                'deal_id' => 'nullable|integer|exists:deals,id',
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'assigned_to' => 'nullable|integer|exists:users,id',
                'title' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
                'tax_amount' => 'nullable|numeric|min:0',
                'discount_amount' => 'nullable|numeric|min:0',
                'valid_until' => 'nullable|date',
                'status' => 'nullable|in:draft,sent,accepted,rejected,expired',
                'notes' => 'nullable|string',
            ]);

            if (
                !$request->post('deal_id') &&
                !$request->post('lead_id') &&
                !$request->post('customer_id')
            ) {
                throw new ApiStatusZeroException(
                    'deal, lead or customer is required'
                );
            }

            $quotation = Quotation::find($request->post('id'));

            if (!$quotation) {
                throw new ApiStatusZeroException('quotation not found');
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

            $oldStatus = $quotation->status;
            $oldAssignedTo = $quotation->assigned_to;
            $quotation->deal_id = $request->post('deal_id');
            $quotation->lead_id = $request->post('lead_id');
            $quotation->customer_id = $request->post('customer_id');
            $quotation->assigned_to = $request->post('assigned_to');
            $quotation->title = $request->post('title');
            $quotation->amount = $amount;
            $quotation->tax_amount = $taxAmount;
            $quotation->discount_amount = $discountAmount;
            $quotation->total_amount = $totalAmount;
            $quotation->valid_until = $request->post('valid_until');
            $quotation->status = $request->post('status', 'draft');
            $quotation->notes = $request->post('notes');

            $quotation->save();

            if ($quotation->assigned_to && $quotation->assigned_to != $oldAssignedTo) {
                Notification::create([
                    'user_id' => $quotation->assigned_to,
                    'title' => 'Quotation Assigned',
                    'message' => 'A new quotation has been assigned to you.',
                ]);
            }

            if ($quotation->status != $oldStatus) {
                QuotationStatusHistory::create([
                    'quotation_id' => $quotation->id,
                    'status' => $quotation->status,
                    'comment' => 'Quotation status updated',
                ]);
            }

            $this->response['msg'] = 'quotation updated successfully';
            $this->response['data'] = $quotation;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:quotations,id',
            ]);

            $quotation = Quotation::find($request->post('id'));

            if (!$quotation) {
                throw new ApiStatusZeroException('quotation not found');
            }

            $quotation->delete();

            $this->response['msg'] = 'quotation deleted successfully';

            return response()->json($this->response);
        });
    }
}
