<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException as ExceptionApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Lead;
use App\Models\Customer;
use Illuminate\Http\Request;

class NoteController extends Controller
{

    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'note' => 'required|string',
            ]);

            if (!$request->post('lead_id') && !$request->post('customer_id')) {
                throw new ExceptionApiStatusZeroException(
                    'lead or customer is required'
                );
            }

            if ($request->post('lead_id') && $request->post('customer_id')) {
                throw new ExceptionApiStatusZeroException(
                    'note can belong to lead or customer'
                );
            }

            $note = Note::create([
                'lead_id' => $request->post('lead_id'),
                'customer_id' => $request->post('customer_id'),
                'note' => $request->post('note'),
            ]);

            $this->response['msg'] = 'note saved successfully';
            $this->response['data'] = $note;

            return response()->json($this->response);
        });
    }

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'lead_id' => 'nullable|integer|exists:leads,id',
                'customer_id' => 'nullable|integer|exists:customers,id',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->get('per_page', 10);

            $query = Note::with('lead', 'customer')
                ->orderBy('id', 'desc');

            if ($request->post('lead_id') !== null) {
                $query->where('lead_id', $request->post('lead_id'));
            }

            if ($request->post('customer_id') !== null) {
                $query->where('customer_id', $request->post('customer_id'));
            }

            $notes = $query->paginate($perPage);

            $this->response['msg'] = 'note list';
            $this->response['data'] = $notes;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer',
            ]);

            $note = Note::with('lead', 'customer')
                ->find($request->post('id'));

            if (!$note) {
                throw new ExceptionApiStatusZeroException('note not found');
            }

            $this->response['msg'] = 'note detail';
            $this->response['data'] = $note;

            return response()->json($this->response);
        });
    }

    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:notes,id',
                'note' => 'required|string',
            ]);

            $note = Note::find($request->post('id'));

            if (!$note) {
                throw new ExceptionApiStatusZeroException('note not found');
            }

            $note->note = $request->post('note');
            $note->save();

            $this->response['msg'] = 'note updated successfully';
            $this->response['data'] = $note;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:notes,id',
            ]);

            $note = Note::find($request->post('id'));

            if (!$note) {
                throw new ExceptionApiStatusZeroException('note not found');
            }

            $note->delete();

            $this->response['msg'] = 'note deleted successfully';
            $this->response['data'] = [];

            return response()->json($this->response);
        });
    }
}
