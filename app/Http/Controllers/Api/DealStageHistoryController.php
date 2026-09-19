<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DealStageHistory;


class DealStageHistoryController extends Controller
{

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'deal_id' => 'required|integer|exists:deals,id',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $history = DealStageHistory::where(
                'deal_id',
                $request->post('deal_id')
            )
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'deal stage history';
            $this->response['data'] = $history;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:deal_stage_histories,id',
            ]);

            $history = DealStageHistory::with('deal')
                ->find($request->post('id'));

            if (!$history) {
                throw new ApiStatusZeroException(
                    'deal stage history not found'
                );
            }

            $this->response['msg'] = 'deal stage history detail';
            $this->response['data'] = $history;

            return response()->json($this->response);
        });
    }
}
