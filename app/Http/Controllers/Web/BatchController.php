<?php

namespace App\Http\Controllers\Web;

use App\Models\Batch;
use App\Models\Location;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BatchController
{
    public function index(): View
    {
        $batches = Batch::with(['product','location'])->orderBy('id')->get();
        return view('batches.index', compact('batches'));
    }

    public function create(): View
    {
        $products = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        return view('batches.create', compact('products','locations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'name' => ['required','string','max:255'],
            'location_id' => ['required','exists:locations,id'],
            'quantity' => ['required','integer','min:0'],
            'expiry_date' => ['nullable','date'],
        ]);
        Batch::create($data);
        return redirect()->route('batches.index')->with('status', 'Lot créé');
    }

    public function edit(int $id): View
    {
        $batch = Batch::findOrFail($id);
        $products = Product::orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        return view('batches.edit', compact('batch','products','locations'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $batch = Batch::findOrFail($id);
        $data = $request->validate([
            'product_id' => ['required','exists:products,id'],
            'name' => ['required','string','max:255'],
            'location_id' => ['required','exists:locations,id'],
            'quantity' => ['required','integer','min:0'],
            'expiry_date' => ['nullable','date'],
        ]);
        $batch->update($data);
        return redirect()->route('batches.index')->with('status', 'Lot mis à jour');
    }

    public function destroy(int $id): RedirectResponse
    {
        $batch = Batch::findOrFail($id);
        $batch->delete();
        return back()->with('status', 'Lot supprimé');
    }
}
