<?php

namespace App\Http\Controllers\Web;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductController
{
    public function index(): View
    {
        $products = Product::orderBy('id')->get();
        return view('products.index', compact('products'));
    }

    public function create(): View
    {
        return view('products.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'sku' => ['required','string','max:255','unique:products,sku'],
        ]);
        Product::create($data);
        return redirect()->route('products.index')->with('status', 'Produit créé');
    }

    public function edit(int $id): View
    {
        $product = Product::findOrFail($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $data = $request->validate([
            'name' => ['required','string','max:255'],
            'sku' => ['required','string','max:255','unique:products,sku,'.$product->id],
        ]);
        $product->update($data);
        return redirect()->route('products.index')->with('status', 'Produit mis à jour');
    }

    public function destroy(int $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return back()->with('status', 'Produit supprimé');
    }
}
