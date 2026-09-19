<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\QuotationStatusHistory;
use Illuminate\Http\Request;


class QuotationStatusHistoryController extends Controller
{

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'quotation_id' => 'required|integer|exists:quotations,id',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $history = QuotationStatusHistory::where(
                'quotation_id',
                $request->post('quotation_id')
            )
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'quotation status history';
            $this->response['data'] = $history;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:quotation_status_histories,id',
            ]);

            $history = QuotationStatusHistory::with('quotation')
                ->find($request->post('id'));

            if (!$history) {
                throw new ApiStatusZeroException(
                    'quotation status history not found'
                );
            }

            $this->response['msg'] = 'quotation status history detail';
            $this->response['data'] = $history;

            return response()->json($this->response);
        });
    }

    
}
