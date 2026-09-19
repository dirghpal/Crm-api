<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\InvoiceStatusHistory;
use Illuminate\Http\Request;


class InvoiceStatusHistoryController extends Controller
{

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'invoice_id' => 'required|integer|exists:invoices,id',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $history = InvoiceStatusHistory::where(
                'invoice_id',
                $request->post('invoice_id')
            )
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'invoice status history';
            $this->response['data'] = $history;

            return response()->json($this->response);
        });
    }


    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:invoice_status_histories,id',
            ]);

            $history = InvoiceStatusHistory::with('invoice')
                ->find($request->post('id'));

            if (!$history) {
                throw new ApiStatusZeroException(
                    'invoice status history not found'
                );
            }

            $this->response['msg'] = 'invoice status history detail';
            $this->response['data'] = $history;

            return response()->json($this->response);
        });
    }
}
