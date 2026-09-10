<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Lead;

class LeadController extends Controller
{
    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'source' => 'nullable|in:website,referral,social_media,advertisement,other',
                'status' => 'nullable|in:new,contacted,qualified,lost',
                'notes' => 'nullable|string'
            ]);

            $lead = Lead::create([
                'name' => $request->post('name'),
                'email' => $request->post('email'),
                'phone' => $request->post('phone'),
                'company' => $request->post('company'),
                'source' => $request->post('source'),
                'status' => $request->post('status', 'new'),
                'notes' => $request->post('notes'),

            ]);

            $this->response['msg'] = 'lead seved successfully';
            $this->response['data'] = $lead;

            return response()->json($this->response);
        });
    }

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'name' => 'nullable|string',
                'status' => 'nullable|string',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perpage = $request->get('per_Page', 10);

            $query = Lead::orderBy('id', 'desc');

            if ($request->post('name') !== null) {
                $query->where('name', 'like', '%' . $request->post('name') . '%');
            }

            if ($request->post('status') !== null) {
                $query->where('status', $request->post('status'));
            }

            $leads = $query->paginate($perpage);

            $this->response['msg'] = 'lead list';
            $this->response['data'] = $leads;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer',
            ]);

            $lead = Lead::find($request->post('id'));

            if (!$lead) {
                throw new ApiStatusZeroException('lead not found');
            }

            $this->response['msg'] = 'lead detail';
            $this->response['data'] = $lead;

            return response()->json($this->response);
        });
    }

    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'nullable|email',
                'phone' => 'nullable|string|max:20',
                'company' => 'nullable|string|max:255',
                'source' => 'nullable|in:website,referral,social_media,advertisement,other',
                'status' => 'nullable|in:new,contacted,qualified,lost',
                'notes' => 'nullable|string',
            ]);

            $lead = Lead::find($request->post('id'));

            if (!$lead) {
                throw new ApiStatusZeroException('lead not found');
            }

            $lead->name = $request->post('name');
            $lead->email = $request->post('email');
            $lead->phone = $request->post('phone');
            $lead->company = $request->post('company');
            $lead->source = $request->post('source');
            $lead->status = $request->post('status', $lead->status);
            $lead->notes = $request->post('notes');
            $lead->save();

            $this->response['msg'] = 'lead updates successfully';
            $this->response['data'] = $lead;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {

        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer',
            ]);

            $lead = Lead::find($request->post('id'));

            if (!$lead) {
                throw new ApiStatusZeroException('lead not found');
            }

            $lead->delete();

            $this->response['msg'] = 'lead deleted successfully';
            $this->response['data'] = [];

            return response()->json($this->response);
        });
    }

}
