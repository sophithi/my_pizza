<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\InventoryMovement;
use App\Http\Requests\StoreInventoryRequest;
use App\Http\Requests\UpdateInventoryRequest;
use App\Traits\ExportableSpreadsheet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Style\DataType;

class InventoryController extends Controller
{
    use ExportableSpreadsheet;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $request->validate([
            'chart_from' => 'nullable|date_format:Y-m-d|before_or_equal:today',
            'chart_to' => 'nullable|date_format:Y-m-d|after_or_equal:chart_from|before_or_equal:today',
            'chart_product_id' => 'nullable|integer|exists:products,id',
        ]);
        $chartTo = $request->input('chart_to') ?: now()->toDateString();
        $chartFrom = $request->input('chart_from') ?: \Carbon\Carbon::parse($chartTo)->subDays(29)->toDateString();
        if (\Carbon\Carbon::parse($chartFrom)->diffInDays(\Carbon\Carbon::parse($chartTo), false) < 0
            || \Carbon\Carbon::parse($chartFrom)->diffInDays(\Carbon\Carbon::parse($chartTo)) > 366) {
            throw \Illuminate\Validation\ValidationException::withMessages(['chart_from' => 'សូមជ្រើសរយៈពេលមិនលើសពី ៣៦៦ ថ្ងៃ។']);
        }
        $analytics = app(\App\Services\InventoryAnalyticsService::class)->dashboard($chartFrom, $chartTo, $request->filled('chart_product_id') ? (int) $request->input('chart_product_id') : null);
        $statsRecord = Inventory::toBase()->selectRaw("
            COUNT(*) as total,
            COUNT(CASE WHEN quantity > 0 AND quantity > COALESCE(reorder_level, 0) THEN 1 END) as in_stock,
            COUNT(CASE WHEN quantity > 0 AND quantity <= COALESCE(reorder_level, 0) THEN 1 END) as low_stock,
            COUNT(CASE WHEN quantity <= 0 THEN 1 END) as out_stock
        ")->first();

        $stats = [
            'total' => (int) ($statsRecord->total ?? 0),
            'in_stock' => (int) ($statsRecord->in_stock ?? 0),
            'low_stock' => (int) ($statsRecord->low_stock ?? 0),
            'out_stock' => (int) ($statsRecord->out_stock ?? 0),
        ];

        $sort = $request->get('sort');
        $period = $request->get('period');
        [$fromDate, $toDate] = $this->getDateRange($request);
        $hasDateRange = (bool) ($fromDate || $toDate);
        $movementDate = $this->movementDateFromRequest($request);
        $hasMovementFilter = $hasDateRange || $movementDate || in_array($period, ['month', 'year'], true);
        $query = Inventory::with('product')->withExists('movements');

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('warehouse_location', 'like', "%{$search}%")
                    ->orWhereHas('product', function ($pq) use ($search) {
                        $pq->where('name', 'like', "%{$search}%")
                            ->orWhere('category', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            if ($request->get('status') === 'out') {
                $query->where('quantity', '<=', 0);
            } elseif ($request->get('status') === 'low') {
                $query->whereRaw('quantity > 0 AND quantity <= COALESCE(reorder_level, 0)');
            } elseif ($request->get('status') === 'in') {
                $query->whereRaw('quantity > 0 AND quantity > COALESCE(reorder_level, 0)');
            }
        }

        if ($request->filled('warehouse')) {
            $query->whereRaw('LOWER(warehouse_location) = ?', [strtolower($request->get('warehouse'))]);
        }

        if ($hasDateRange) {
            $query->whereHas('movements', function ($movementQuery) use ($fromDate, $toDate) {
                if ($fromDate && $toDate) {
                    $movementQuery->whereDate('created_at', '>=', $fromDate)
                        ->whereDate('created_at', '<=', $toDate);
                } elseif ($fromDate) {
                    $movementQuery->whereDate('created_at', '>=', $fromDate);
                } else {
                    $movementQuery->whereDate('created_at', '<=', $toDate);
                }
            });
        } elseif ($movementDate) {
            $query->whereHas('movements', function ($movementQuery) use ($movementDate) {
                $movementQuery->whereDate('created_at', $movementDate);
            });
        } elseif ($period === 'month') {
            $query->whereHas('movements', function ($movementQuery) {
                $movementQuery->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            });
        } elseif ($period === 'year') {
            $query->whereHas('movements', function ($movementQuery) {
                $movementQuery->whereYear('created_at', now()->year);
            });
        }

        if ($hasMovementFilter || $sort === 'day') {
            $query = $query->withMax('movements as last_movement_at', 'created_at')
                ->orderByDesc('last_movement_at');
        } else {
            $query = $query->orderByDesc('id');
        }

        $inventories = $query->paginate(15)->withQueryString();
        $productsWithOrders = \App\Models\OrderItem::whereIn('product_id', $inventories->pluck('product_id'))
            ->distinct()->pluck('product_id');
        $orderQuantities = \App\Models\OrderItem::whereIn('product_id', $inventories->pluck('product_id'))
            ->whereHas('order', function ($orders) use ($fromDate, $toDate, $movementDate, $period) {
                $orders->where('status', '!=', 'cancelled');
                if ($fromDate || $toDate) {
                    if ($fromDate) $orders->whereDate('order_date', '>=', $fromDate);
                    if ($toDate) $orders->whereDate('order_date', '<=', $toDate);
                } elseif ($movementDate) {
                    $orders->whereDate('order_date', $movementDate);
                } elseif ($period === 'month') {
                    $orders->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year);
                } elseif ($period === 'year') {
                    $orders->whereYear('order_date', now()->year);
                }
            })
            ->selectRaw("product_id, SUM(CASE WHEN item_type = 'sale' THEN quantity ELSE 0 END) as sold, SUM(CASE WHEN item_type = 'free' THEN quantity ELSE 0 END) as free_qty")
            ->groupBy('product_id')->get()->keyBy('product_id');
        $movementsByInventory = $this->movementsByInventory($request);

        // Distinct warehouse locations across ALL inventory (not just the current
        // filtered/paginated page), so the filter dropdown always shows every option.
        $warehouses = Inventory::whereNotNull('warehouse_location')
            ->where('warehouse_location', '!=', '')
            ->distinct()
            ->orderBy('warehouse_location')
            ->pluck('warehouse_location');

        return view('inventory.index', compact('inventories', 'productsWithOrders', 'orderQuantities', 'stats', 'movementDate', 'movementsByInventory', 'warehouses', 'fromDate', 'toDate', 'analytics', 'chartFrom', 'chartTo'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $products = \App\Models\Product::doesntHave('inventory')->get();
        return view('inventory.create', compact('products'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInventoryRequest $request)
    {
        $inventory = DB::transaction(function () use ($request) {
            $inventory = Inventory::create($request->validated());
            $this->recordManualMovement($inventory, 'stock_create', (int) $inventory->quantity, 0, (int) $inventory->quantity, 'Initial inventory created');

            return $inventory;
        });

        return redirect()->route('inventory.show', $inventory)->with('success', 'បានបង្កើតស្តុកទំនិញដោយជោគជ័យ។');
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Inventory $inventory)
    {
        $inventory->load('product');

        $movementsQuery = $inventory->movements()->with(['user', 'order'])->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $movementsQuery->where(function ($q) use ($search) {
                $q->where('note', 'like', "%{$search}%")
                    ->orWhere('order_id', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('type')) {
            $type = $request->type;
            if ($type === 'in') {
                $movementsQuery->where('quantity_change', '>', 0);
            } elseif ($type === 'out') {
                $movementsQuery->where('quantity_change', '<', 0);
            } else {
                $movementsQuery->where('type', $type);
            }
        }

        [$fromDate, $toDate] = $this->getDateRange($request);
        if ($fromDate && $toDate) {
            $movementsQuery->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=', $toDate);
        } elseif ($fromDate) {
            $movementsQuery->whereDate('created_at', '>=', $fromDate);
        } elseif ($toDate) {
            $movementsQuery->whereDate('created_at', '<=', $toDate);
        } elseif ($request->get('period') === 'today') {
            $movementsQuery->whereDate('created_at', today());
        } elseif ($request->get('period') === 'yesterday') {
            $movementsQuery->whereDate('created_at', today()->subDay());
        } elseif ($request->get('period') === 'month') {
            $movementsQuery->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        }

        $totalIn = (int) (clone $movementsQuery)->where('quantity_change', '>', 0)->sum('quantity_change');
        $totalOut = abs((int) (clone $movementsQuery)->where('quantity_change', '<', 0)->sum('quantity_change'));
        $netChange = $totalIn - $totalOut;
        $totalTransactions = (clone $movementsQuery)->count();
        // Count current order items once, never repeated deduction/edit movements.
        $items = \App\Models\OrderItem::where('product_id', $inventory->product_id)
            ->whereHas('order', function ($query) use ($request, $fromDate, $toDate) {
                $query->where('status', '!=', 'cancelled');
                if ($fromDate || $toDate) {
                    if ($fromDate) $query->whereDate('order_date', '>=', $fromDate);
                    if ($toDate) $query->whereDate('order_date', '<=', $toDate);
                } elseif ($request->input('period') === 'today') {
                    $query->whereDate('order_date', today());
                } elseif ($request->input('period') === 'yesterday') {
                    $query->whereDate('order_date', today()->subDay());
                } elseif ($request->input('period') === 'month') {
                    $query->whereMonth('order_date', now()->month)->whereYear('order_date', now()->year);
                }
            });
        $saleQty = (int) (clone $items)->where('item_type', 'sale')->sum('quantity');
        $freeQty = (int) (clone $items)->where('item_type', 'free')->sum('quantity');

        if (in_array($request->input('item_type'), ['sale', 'free'], true)) {
            $movementsQuery->where('item_type', $request->input('item_type'));
        } elseif ($request->input('item_type') === 'unclassified') {
            $movementsQuery->whereNull('item_type')->whereIn('type', ['order_deduct', 'order_adjust', 'order_restore']);
        }

        $movements = $movementsQuery->paginate(15)->withQueryString();

        return view('inventory.show', compact('inventory', 'movements', 'totalIn', 'totalOut', 'netChange', 'totalTransactions', 'saleQty', 'freeQty'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Inventory $inventory)
    {
        return view('inventory.edit', compact('inventory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInventoryRequest $request, Inventory $inventory)
    {
        DB::transaction(function () use ($request, $inventory) {
            $inventory = Inventory::lockForUpdate()->findOrFail($inventory->id);
            $validated = $request->validated();
            $before = (int) $inventory->quantity;
            $inventory->update($validated);
            $after = (int) $inventory->quantity;

            if ($before !== $after) {
                $this->recordManualMovement($inventory, 'manual_adjust', $after - $before, $before, $after, 'Inventory edited manually');
            }
        });

        return redirect()->route('inventory.show', $inventory)->with('success', 'បានកែប្រែស្តុកទំនិញដោយជោគជ័យ។');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Inventory $inventory)
    {
        DB::transaction(function () use ($inventory) {
            $inventory = Inventory::lockForUpdate()->findOrFail($inventory->id);
            if ($inventory->movements()->exists()
                || \App\Models\OrderItem::where('product_id', $inventory->product_id)->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'inventory' => 'មិនអាចលុបស្តុកដែលមានប្រវត្តិចលនា ឬមានការបញ្ជាទិញពាក់ព័ន្ធបានទេ។ សូមប្រើការកែតម្រូវស្តុក។',
                ]);
            }
            if ((int) $inventory->quantity !== 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'inventory' => 'មិនអាចលុបស្តុកដែលមានចំនួនមិនស្មើសូន្យបានទេ។',
                ]);
            }
            $inventory->delete();
        });
        return redirect()->route('inventory.index')->with('success', 'បានលុបស្តុកទំនិញដោយជោគជ័យ។');
    }

    /**
     * Quick update the quantity of an inventory item.
     */
    public function quickUpdate($id)
    {
        $inventory = Inventory::findOrFail($id);
        $validated = request()->validate([
            'quantity' => 'required|integer|min:0',
            'note' => 'nullable|string|max:255',
        ]);
        $quantity = (int) $validated['quantity'];
        $note = trim($validated['note'] ?? '') ?: 'Quick stock update';

        DB::transaction(function () use ($inventory, $quantity, $note) {
            $inventory = Inventory::lockForUpdate()->findOrFail($inventory->id);
            $before = (int) $inventory->quantity;
            $after = $quantity;
            $inventory->update(['quantity' => $after]);

            if ($before !== $after) {
                $this->recordManualMovement($inventory, 'quick_adjust', $after - $before, $before, $after, $note);
            }
        });

        return redirect()->back()->with('success', 'បានកែប្រែចំនួនស្តុកដោយជោគជ័យ។');
    }

    public function restock(Inventory $inventory)
    {
        $validated = request()->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        $addedQuantity = (int) $validated['quantity'];

        DB::transaction(function () use ($inventory, $addedQuantity) {
            $inventory = Inventory::lockForUpdate()->findOrFail($inventory->id);
            $before = (int) $inventory->quantity;
            $inventory->increment('quantity', $addedQuantity);
            $after = (int) $inventory->fresh()->quantity;

            $this->recordManualMovement($inventory, 'stock_restock', $addedQuantity, $before, $after, 'Stock added to inventory');
        });

        return redirect()->back()->with('success', 'បានបន្ថែមចំនួនទំនិញចូលស្តុកដោយជោគជ័យ។');
    }

    public function reduce(Inventory $inventory)
    {
        $validated = request()->validate([
            'quantity' => 'required|integer|min:1',
            'reason' => 'required|string|max:255',
        ]);

        $removedQuantity = (int) $validated['quantity'];
        $reason = trim($validated['reason']);

        if ($removedQuantity > (int) $inventory->quantity) {
            return redirect()->back()->withErrors([
                'quantity' => 'ចំនួនដែលត្រូវកាត់ចេញមិនអាចលើសពីចំនួនក្នុងស្តុកបច្ចុប្បន្ន (' . number_format($inventory->quantity) . ') ឡើយ។'
            ]);
        }

        DB::transaction(function () use ($inventory, $removedQuantity, $reason) {
            $inventory = Inventory::lockForUpdate()->findOrFail($inventory->id);
            $before = (int) $inventory->quantity;
            if ($removedQuantity > $before) {
                throw \Illuminate\Validation\ValidationException::withMessages(['quantity' => 'Insufficient stock. Please refresh and try again.']);
            }
            $inventory->decrement('quantity', $removedQuantity);
            $after = (int) $inventory->fresh()->quantity;

            $this->recordManualMovement($inventory, 'stock_reduce', -$removedQuantity, $before, $after, $reason);
        });

        return redirect()->back()->with('success', 'បានកាត់ចំនួនទំនិញចេញពីស្តុកដោយជោគជ័យ។');
    }

    private function getDateRange(Request $request): array
    {
        $from = $request->get('from_date') ?: $request->get('start_date') ?: $request->get('from');
        $to = $request->get('to_date') ?: $request->get('end_date') ?: $request->get('to');

        return [$from ?: null, $to ?: null];
    }

    private function movementDateFromRequest(Request $request): ?string
    {
        if ($request->get('period') === 'today') {
            return today()->toDateString();
        }

        if ($request->get('period') === 'yesterday') {
            return today()->subDay()->toDateString();
        }

        if ($request->filled('date')) {
            return $request->date;
        }

        return null;
    }

    private function movementsByInventory(Request $request)
    {
        $query = $this->visibleMovementGroups($request)
            ->selectRaw('inventory_id, SUM(CASE WHEN net_change < 0 THEN ABS(net_change) ELSE 0 END) as cut_out')
            ->selectRaw('SUM(CASE WHEN net_change > 0 THEN net_change ELSE 0 END) as added_back')
            ->selectRaw('MAX(last_movement_at) as last_movement_at')
            ->groupBy('inventory_id');

        return $query->get()->keyBy('inventory_id');
    }

    private function visibleMovementGroups(Request $request)
    {
        $groups = InventoryMovement::query()
            ->selectRaw('inventory_id')
            ->selectRaw('SUM(quantity_change) as net_change')
            ->selectRaw('MAX(created_at) as last_movement_at')
            ->groupBy('inventory_id')
            ->groupBy(DB::raw('COALESCE(order_id, id)'))
            ->groupBy('created_at');

        $this->applyMovementDateFilter($groups, $request);

        return DB::query()
            ->fromSub($groups, 'movement_groups')
            ->where('net_change', '<>', 0);
    }

    private function applyMovementDateFilter($query, Request $request): void
    {
        $period = $request->get('period');
        [$fromDate, $toDate] = $this->getDateRange($request);
        $movementDate = $this->movementDateFromRequest($request);

        if ($fromDate && $toDate) {
            $query->whereDate('created_at', '>=', $fromDate)
                ->whereDate('created_at', '<=', $toDate);
        } elseif ($fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        } elseif ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        } elseif ($movementDate) {
            $query->whereDate('created_at', $movementDate);
        } elseif ($period === 'month') {
            $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
        } elseif ($period === 'year') {
            $query->whereYear('created_at', now()->year);
        }
    }

    private function recordManualMovement(Inventory $inventory, string $type, int $quantityChange, int $quantityBefore, int $quantityAfter, string $note): void
    {
        InventoryMovement::create([
            'inventory_id' => $inventory->id,
            'product_id' => $inventory->product_id,
            'user_id' => auth()->id(),
            'type' => $type,
            'quantity_change' => $quantityChange,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'note' => $note,
        ]);
    }

    public function exportExcel(Request $request)
    {
        $inventories = $this->getInventoriesForFilter($request);
        $spreadsheet = $this->createBrandedSpreadsheet('Inventory', 'របាយការណ៍ស្តុកទំនិញ', 8);
        $sheet = $spreadsheet->getActiveSheet();

        $headers = ['ល.រ', 'ទំនិញ', 'ប្រភេទ', 'ទីតាំង', 'ចំនួន', 'កម្រិត', 'ស្ថានភាព', 'កាលបរិច្ឆេទ'];
        $headerRow = 6;

        foreach ($headers as $index => $header) {
            $sheet->setCellValue(chr(65 + $index) . $headerRow, $header);
        }

        $row = 7;
        $number = 1;
        foreach ($inventories as $inv) {
            $status = $inv->quantity <= 0 ? 'អស់' : ($inv->quantity <= $inv->reorder_level ? 'ជិត' : 'មាន');
            $sheet->setCellValue("A{$row}", $number);
            $sheet->setCellValue("B{$row}", $inv->product?->name ?? '—');
            $sheet->setCellValue("C{$row}", $inv->product?->category ?? '—');
            $sheet->setCellValue("D{$row}", $inv->warehouse_location ?? '—');
            $sheet->setCellValue("E{$row}", $inv->quantity);
            $sheet->setCellValue("F{$row}", $inv->reorder_level);
            $sheet->setCellValue("G{$row}", $status);
            $sheet->setCellValue("H{$row}", $inv->updated_at?->format('d/m/Y') ?? '—');
            $row++;
            $number++;
        }

        $lastRow = max(7, $row - 1);
        $this->styleTableHeaders($sheet, "A{$headerRow}:H{$headerRow}", "A{$headerRow}:H{$lastRow}");
        $this->applyStripeRows($sheet, 7, $lastRow);

        // Set column widths
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(10);
        $sheet->getColumnDimension('F')->setWidth(12);
        $sheet->getColumnDimension('G')->setWidth(12);
        $sheet->getColumnDimension('H')->setWidth(13);

        return $this->downloadSpreadsheet($spreadsheet, 'inventory_' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $inventories = $this->getInventoriesForFilter($request);
        return view('inventory.pdf', compact('inventories'));
    }

    private function getInventoriesForFilter(Request $request)
    {
        $period = $request->get('period');
        [$fromDate, $toDate] = $this->getDateRange($request);
        $hasDateRange = (bool) ($fromDate || $toDate);
        $movementDate = $this->movementDateFromRequest($request);
        $hasMovementFilter = $hasDateRange || $movementDate || in_array($period, ['month', 'year'], true);

        $query = Inventory::with('product')
            ->when($request->filled('search'), function ($q) use ($request) {
                $search = $request->search;
                $q->where(function ($sq) use ($search) {
                    $sq->where('warehouse_location', 'like', "%{$search}%")
                        ->orWhereHas('product', function ($pq) use ($search) {
                            $pq->where('name', 'like', "%{$search}%")
                                ->orWhere('category', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($q) use ($request) {
                if ($request->status === 'out') {
                    $q->where('quantity', '<=', 0);
                } elseif ($request->status === 'low') {
                    $q->whereRaw('quantity > 0 AND quantity <= reorder_level');
                } elseif ($request->status === 'in') {
                    $q->whereRaw('quantity > reorder_level');
                }
            })
            ->when($request->filled('warehouse'), function ($q) use ($request) {
                $q->whereRaw('LOWER(warehouse_location) = ?', [strtolower($request->warehouse)]);
            });

        if ($hasDateRange) {
            $query->whereHas('movements', function ($movementQuery) use ($fromDate, $toDate) {
                if ($fromDate && $toDate) {
                    $movementQuery->whereDate('created_at', '>=', $fromDate)
                        ->whereDate('created_at', '<=', $toDate);
                } elseif ($fromDate) {
                    $movementQuery->whereDate('created_at', '>=', $fromDate);
                } else {
                    $movementQuery->whereDate('created_at', '<=', $toDate);
                }
            });
        } elseif ($movementDate) {
            $query->whereHas('movements', function ($movementQuery) use ($movementDate) {
                $movementQuery->whereDate('created_at', $movementDate);
            });
        } elseif ($period === 'month') {
            $query->whereHas('movements', function ($movementQuery) {
                $movementQuery->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
            });
        } elseif ($period === 'year') {
            $query->whereHas('movements', function ($movementQuery) {
                $movementQuery->whereYear('created_at', now()->year);
            });
        }

        if ($hasMovementFilter || $request->get('sort') === 'day') {
            $query = $query->withMax('movements as last_movement_at', 'created_at')
                ->orderByDesc('last_movement_at');
        } else {
            $query = $query->orderByDesc('id');
        }

        return $query->get();
    }
}
