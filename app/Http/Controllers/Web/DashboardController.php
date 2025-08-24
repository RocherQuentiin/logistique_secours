<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Batch;
use App\Models\Location;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $useMock = request()->boolean('mock');
    $locationId = request()->integer('location_id');
    $period = (int) request()->get('period', 60);
    $productId = request()->integer('product_id');
    $allowed = [30, 60, 90, 180];
    if (!in_array($period, $allowed, true)) { $period = 60; }
    // Default low stock threshold (per product total). Can be tuned later via config/env.
    $lowThreshold = 10;

        if ($useMock) {
            $locations = Location::orderBy('name')->get(['id','name']);
            $products = Product::orderBy('name')->get(['id','name']);
            $today = Carbon::today();
            $months = [];
            $cursor = $today->copy()->startOfMonth();
            $monthsCount = max(3, min(6, (int) ceil($period / 30)));
            for ($i = 0; $i < $monthsCount; $i++) { $months[] = $cursor->copy(); $cursor->addMonth(); }

            $sampleByLoc = collect([
                ['label' => 'Dépôt A', 'qty' => 1200],
                ['label' => 'Dépôt B', 'qty' => 870],
                ['label' => 'Camion 1', 'qty' => 420],
                ['label' => 'Camion 2', 'qty' => 260],
                ['label' => 'Gymnase', 'qty' => 1125],
            ]);
            if ($locationId) {
                $locName = optional($locations->firstWhere('id', $locationId))->name ?? 'Lieu sélectionné';
                $byLocation = collect([[ 'label' => $locName, 'qty' => 600 ]]);
            } else {
                $byLocation = $sampleByLoc;
            }

            $expCounts = [3, 7, 5, 11, 6, 9];
            $expirations = collect($months)->values()->map(function (Carbon $m, $i) use ($expCounts) {
                return [ 'label' => $m->isoFormat('MMM YYYY'), 'count' => $expCounts[$i] ?? 0 ];
            });

            // Sample top products; if a specific product is selected, reduce to that entry
            $sampleTop = collect([
                ['label' => 'Gants nitrile', 'qty' => 950],
                ['label' => 'Masques FFP2', 'qty' => 720],
                ['label' => 'Sérum phy 500ml', 'qty' => 520],
                ['label' => 'Bandages 10cm', 'qty' => 480],
                ['label' => 'Garrots', 'qty' => 360],
            ]);
            if ($productId) {
                $pName = optional($products->firstWhere('id', $productId))->name ?? 'Produit sélectionné';
                $sampleTop = collect([[ 'label' => $pName, 'qty' => 500 ]]);
            }

            // Mock alert lists
            $mockExpired = collect([
                ['product' => 'Masques FFP2', 'batch' => 'FFP2-2024-01', 'location' => 'Dépôt A', 'qty' => 40, 'expiry_date' => $today->copy()->subDays(10)->toDateString()],
                ['product' => 'Gants nitrile', 'batch' => 'GN-2023-10', 'location' => 'Camion 1', 'qty' => 15, 'expiry_date' => $today->copy()->subDays(2)->toDateString()],
            ]);
            $mockExpiring = collect([
                ['product' => 'Sérum phy 500ml', 'batch' => 'SP-500-0425', 'location' => 'Dépôt B', 'qty' => 20, 'expiry_date' => $today->copy()->addDays(14)->toDateString()],
                ['product' => 'Bandages 10cm', 'batch' => 'BD10-0525', 'location' => 'Gymnase', 'qty' => 12, 'expiry_date' => $today->copy()->addDays(28)->toDateString()],
            ]);
            $mockLowStock = collect([
                ['product_id' => 1, 'product_name' => 'Garrots', 'qty' => 6],
                ['product_id' => 2, 'product_name' => 'Bandages 10cm', 'qty' => 9],
            ]);

            return view('home', [
                'kpis' => [
                    'Produits' => 42,
                    'Lots' => 128,
                    'Quantité totale' => 3875,
                    'Péremptions ≤ ' . $period . 'j' => 9,
                ],
                'alerts' => [
                    'expired' => 2,
                    'expiring' => 9,
                    'lowStock' => 3,
                ],
                'lists' => [
                    'expired' => $mockExpired,
                    'expiring' => $mockExpiring,
                    'lowStock' => $mockLowStock,
                ],
                'lowThreshold' => $lowThreshold,
                'byLocation' => $byLocation,
                'topProducts' => $sampleTop,
                'expirations' => $expirations,
                'mock' => true,
                'filters' => [ 'location_id' => $locationId, 'period' => $period, 'product_id' => $productId ],
                'locations' => $locations,
                'products' => $products,
            ]);
        }

        // Real data with filters
        $base = Batch::query();
        if ($locationId) { $base->where('location_id', $locationId); }
        if ($productId) { $base->where('product_id', $productId); }
        $productCount = (clone $base)->distinct('product_id')->count('product_id');
        if (!$locationId && $productCount === 0) { $productCount = Product::count(); }
        $batchCount = (clone $base)->count();
        $totalQty = (int) (clone $base)->sum('quantity');

        $today = Carbon::today();
        $until = Carbon::today()->addDays($period);
        $expiringSoon = (clone $base)->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $until])
            ->count();
        $expired = (clone $base)->whereNotNull('expiry_date')
            ->where('expiry_date', '<', $today)
            ->count();

        // Low stock: number of products whose total quantity <= threshold (within current filters)
        $lowStock = (clone $base)
            ->selectRaw('product_id, SUM(quantity) as qty')
            ->groupBy('product_id')
            ->having('qty', '<=', $lowThreshold)
            ->get()
            ->count();

        // Quantities by location
        if ($locationId) {
            $sum = (clone $base)->sum('quantity');
            $locName = optional(Location::find($locationId))->name ?? 'Non défini';
            $byLocation = collect([[ 'label' => $locName, 'qty' => (int) $sum ]]);
        } else {
            $byLocation = Batch::selectRaw('location_id, SUM(quantity) as qty')
                ->groupBy('location_id')
                ->with('location:id,name')
                ->get()
                ->map(fn($r) => [
                    'label' => $r->location?->name ?? 'Non défini',
                    'qty' => (int) $r->qty,
                ]);
        }

        // Top 5 products by total quantity
        $topProducts = (clone $base)
            ->selectRaw('product_id, SUM(quantity) as qty')
            ->groupBy('product_id')
            ->with('product:id,name')
            ->orderByDesc('qty')
            ->limit(5)
            ->get()
            ->map(fn($r) => [
                'label' => $r->product?->name ?? 'Non défini',
                'qty' => (int) $r->qty,
            ]);

        // Expirations by month (next 6 months)
    $months = [];
        $cursor = $today->copy()->startOfMonth();
        $monthsCount = max(3, min(6, (int) ceil($period / 30)));
        for ($i = 0; $i < $monthsCount; $i++) {
            $months[] = $cursor->copy();
            $cursor->addMonth();
        }
        $expirations = collect($months)->map(function (Carbon $m) use ($base) {
            $start = $m->copy();
            $end = $m->copy()->endOfMonth();
            $count = (clone $base)->whereNotNull('expiry_date')
                ->whereBetween('expiry_date', [$start, $end])
                ->count();
            return [
                'label' => $m->isoFormat('MMM YYYY'),
                'count' => $count,
            ];
        });

        // If database is empty (no products & no batches), offer mock preview instead
        if (!$locationId && $productCount === 0 && $batchCount === 0) {
            return redirect()->to('/?mock=1');
        }

        // Build alert lists
        $expiredList = (clone $base)
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', $today)
            ->with(['product:id,name','location:id,name'])
            ->orderBy('expiry_date')
            ->limit(200)
            ->get(['id','product_id','name','location_id','quantity','expiry_date']);

        $expiringList = (clone $base)
            ->whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $until])
            ->with(['product:id,name','location:id,name'])
            ->orderBy('expiry_date')
            ->limit(200)
            ->get(['id','product_id','name','location_id','quantity','expiry_date']);

        $grouped = (clone $base)
            ->selectRaw('product_id, SUM(quantity) as qty')
            ->groupBy('product_id')
            ->having('qty', '<=', $lowThreshold)
            ->get();
        $prodMap = Product::whereIn('id', $grouped->pluck('product_id')->all())
            ->get(['id','name'])
            ->keyBy('id');
        $lowStockList = $grouped->map(function ($r) use ($prodMap) {
            return [
                'product_id' => $r->product_id,
                'product_name' => $prodMap->get($r->product_id)->name ?? 'Non défini',
                'qty' => (int) ($r->qty ?? 0),
            ];
        })->sortBy('qty')->values();

        $locations = Location::orderBy('name')->get(['id','name']);
        $products = Product::orderBy('name')->get(['id','name']);
        return view('home', [
            'kpis' => [
                'Produits' => $productCount,
                'Lots' => $batchCount,
                'Quantité totale' => $totalQty,
                'Péremptions ≤ ' . $period . 'j' => $expiringSoon,
            ],
            'alerts' => [
                'expired' => $expired,
                'expiring' => $expiringSoon,
                'lowStock' => $lowStock,
            ],
            'lists' => [
                'expired' => $expiredList,
                'expiring' => $expiringList,
                'lowStock' => $lowStockList,
            ],
            'lowThreshold' => $lowThreshold,
            'byLocation' => $byLocation,
            'topProducts' => $topProducts,
            'expirations' => $expirations,
            'mock' => false,
            'filters' => [ 'location_id' => $locationId, 'period' => $period, 'product_id' => $productId ],
            'locations' => $locations,
            'products' => $products,
        ]);
    }
}
