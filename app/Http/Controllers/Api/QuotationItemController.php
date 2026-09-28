<?php

namespace App\Http\Controllers\Api;

use App\Exception\ApiStatusZeroException as ExceptionApiStatusZeroException;
use App\Exceptions\ApiStatusZeroException;
use App\Http\Controllers\Controller;
use App\Models\Quotation;
use App\Models\QuotationItem;
use Illuminate\Http\Request;

class QuotationItemController extends Controller
{
    public function save(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'quotation_id' => 'required|integer|exists:quotations,id',
                'item_name' => 'required|string|max:255',
                'quantity' => 'required|numeric|min:0.01',
                'price' => 'required|numeric|min:0',
            ]);

            $quotation = Quotation::find($request->post('quotation_id'));

            if (!$quotation) {
                throw new ExceptionApiStatusZeroException('quotation not found');
            }

            $quantity = (float) $request->post('quantity');
            $price = (float) $request->post('price');

            $totalAmount = $quantity * $price;

            $item = QuotationItem::create([
                'quotation_id' => $quotation->id,
                'item_name' => $request->post('item_name'),
                'quantity' => $quantity,
                'price' => $price,
                'total_amount' => $totalAmount,
            ]);

            $quotationAmount = $quotation->items()->sum('total_amount');

            $quotation->amount = $quotationAmount;
            $quotation->total_amount =
                $quotationAmount +
                $quotation->tax_amount -
                $quotation->discount_amount;

            $quotation->save();

            $this->response['msg'] = 'quotation item saved successfully';
            $this->response['data'] = $item;

            return response()->json($this->response);
        });
    }

    public function list(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'quotation_id' => 'required|integer|exists:quotations,id',
                'per_page' => 'nullable|integer|min:1|max:100',
            ]);

            $perPage = $request->post('per_page', 10);

            $items = QuotationItem::where(
                'quotation_id',
                $request->post('quotation_id')
            )
                ->orderBy('id', 'desc')
                ->paginate($perPage);

            $this->response['msg'] = 'quotation item list';
            $this->response['data'] = $items;

            return response()->json($this->response);
        });
    }

    public function detail(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:quotation_items,id',
            ]);

            $item = QuotationItem::with('quotation')
                ->find($request->post('id'));

            if (!$item) {
                throw new ExceptionApiStatusZeroException(
                    'quotation item not found'
                );
            }

            $this->response['msg'] = 'quotation item detail';
            $this->response['data'] = $item;

            return response()->json($this->response);
        });
    }

    public function update(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:quotation_items,id',
                'item_name' => 'required|string|max:255',
                'quantity' => 'required|numeric|min:0.01',
                'price' => 'required|numeric|min:0',
            ]);

            $item = QuotationItem::find($request->post('id'));

            if (!$item) {
                throw new ExceptionApiStatusZeroException(
                    'quotation item not found'
                );
            }

            $quantity = (float) $request->post('quantity');
            $price = (float) $request->post('price');

            $item->item_name = $request->post('item_name');
            $item->quantity = $quantity;
            $item->price = $price;
            $item->total_amount = $quantity * $price;

            $item->save();

            $quotation = $item->quotation;

            $quotationAmount = $quotation->items()->sum('total_amount');

            $quotation->amount = $quotationAmount;
            $quotation->total_amount =
                $quotationAmount +
                $quotation->tax_amount -
                $quotation->discount_amount;

            $quotation->save();

            $this->response['msg'] = 'quotation item updated successfully';
            $this->response['data'] = $item;

            return response()->json($this->response);
        });
    }

    public function delete(Request $request)
    {
        return handleApiRequest(function () use ($request) {

            $request->validate([
                'id' => 'required|integer|exists:quotation_items,id',
            ]);

            $item = QuotationItem::find($request->post('id'));

            if (!$item) {
                throw new ExceptionApiStatusZeroException(
                    'quotation item not found'
                );
            }

            $quotation = $item->quotation;
            $item->delete();

            $quotationAmount = $quotation->items()->sum('total_amount');

            $quotation->amount = $quotationAmount;

            $quotation->total_amount =
                $quotationAmount +
                $quotation->tax_amount -
                $quotation->discount_amount;

            $quotation->save();

            $this->response['msg'] = 'quotation item deleted successfully';

            return response()->json($this->response);
        });
    }
}
