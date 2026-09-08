<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Salesperson;
use Illuminate\Http\Request;

class CustomerLocationController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query()
            ->withCount('orders')
            ->with('salesperson')
            ->withMax('orders as last_order_at', 'order_date');

        // Search
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%")
                    ->orWhere('landmark', 'like', "%{$search}%");
            });
        }

        // Filter by location status
        $locFilter = $request->get('location_status', 'all');
        if ($locFilter === 'pinned') {
            $query->whereNotNull('latitude')->whereNotNull('longitude');
        } elseif ($locFilter === 'unpinned') {
            $query->where(function ($q) {
                $q->whereNull('latitude')->orWhereNull('longitude');
            });
        }

        // Stats
        $totalCustomers = Customer::count();
        $pinnedCount = Customer::whereNotNull('latitude')->whereNotNull('longitude')->count();
        $unpinnedCount = $totalCustomers - $pinnedCount;

        // Paginated list for table
        $customers = (clone $query)->latest('id')->paginate(15)->appends($request->query());

        // All pinned customers for the map
        $mapCustomers = Customer::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->select('id', 'name', 'phone', 'address', 'city', 'landmark', 'latitude', 'longitude', 'map_url')
            ->get();

        $salespersons = Salesperson::where('status', 'active')->orderBy('name')->get();
        $deliveries = \App\Models\Delivery::orderBy('delivery_name')->get();
        $allCustomers = Customer::select('id', 'name', 'phone', 'address', 'city', 'latitude', 'longitude', 'landmark')
            ->orderBy('name')
            ->get();

        // Persistent Store Location from Settings
        $storeLocation = self::getStoreLocation();

        return view('customer_locations.index', compact(
            'customers',
            'mapCustomers',
            'allCustomers',
            'totalCustomers',
            'pinnedCount',
            'unpinnedCount',
            'locFilter',
            'salespersons',
            'deliveries',
            'storeLocation'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'map_url' => 'nullable|string|max:500',
            'type' => 'nullable|string',
            'salesperson_id' => 'nullable|exists:salespersons,id',
        ]);

        if (empty($validated['map_url']) && !empty($validated['latitude']) && !empty($validated['longitude'])) {
            $validated['map_url'] = sprintf(
                'https://www.google.com/maps?q=%F,%F',
                $validated['latitude'],
                $validated['longitude']
            );
        }

        if ($request->filled('customer_id')) {
            $customer = Customer::findOrFail($request->customer_id);
            $customer->update($validated);
            $msg = 'បានកំណត់ទីតាំងអតិថិជន "' . $customer->name . '" ជោគជ័យ!';
        } else {
            $customer = Customer::create($validated + ['status' => 'active']);
            $msg = 'បានបង្កើត និងរក្សាទុកទីតាំងអតិថិជនថ្មីជោគជ័យ!';
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => $msg,
                'customer' => $customer
            ]);
        }

        return redirect()->route('customer-locations.index')
            ->with('success', $msg);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'landmark' => 'nullable|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'map_url' => 'nullable|string|max:500',
        ]);

        if (empty($validated['map_url']) && !empty($validated['latitude']) && !empty($validated['longitude'])) {
            $validated['map_url'] = sprintf(
                'https://www.google.com/maps?q=%F,%F',
                $validated['latitude'],
                $validated['longitude']
            );
        }

        $customer->update($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'បានកែប្រែទីតាំងជោគជ័យ!',
                'customer' => $customer
            ]);
        }

        return redirect()->route('customer-locations.index')
            ->with('success', 'បានកែប្រែទីតាំងអតិថិជនដោយជោគជ័យ!');
    }

    public function destroy(Customer $customer)
    {
        $customer->update([
            'latitude' => null,
            'longitude' => null,
            'map_url' => null,
            'landmark' => null,
        ]);

        return redirect()->route('customer-locations.index')
            ->with('success', 'បានលុបទីតាំង GPS ចេញពីអតិថិជនជោគជ័យ!');
    }

    public function quickSave(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'landmark' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        $mapUrl = sprintf(
            'https://www.google.com/maps?q=%F,%F',
            $request->latitude,
            $request->longitude
        );

        $updateData = [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'map_url' => $mapUrl,
        ];

        if ($request->filled('landmark')) {
            $updateData['landmark'] = $request->landmark;
        }
        if ($request->filled('address')) {
            $updateData['address'] = $request->address;
        }

        $customer->update($updateData);

        return response()->json([
            'status' => 'ok',
            'message' => 'បានកត់ត្រាទីតាំងផែនទីជោគជ័យ!',
            'customer' => $customer,
        ]);
    }

    /**
     * Get Persistent Main Office Location Settings
     */
    public static function getStoreLocation(): array
    {
        $file = storage_path('app/store_settings.json');
        if (file_exists($file)) {
            $data = json_decode(file_get_contents($file), true);
            if (is_array($data) && !empty($data['lat']) && !empty($data['lng'])) {
                return [
                    'name' => $data['name'] ?? 'Pizza Happy Family (Main Office)',
                    'address' => $data['address'] ?? 'ភ្នំពេញ',
                    'lat' => (float) $data['lat'],
                    'lng' => (float) $data['lng'],
                    'phone' => $data['phone'] ?? '012 345 678',
                    'radius_1' => (float) ($data['radius_1'] ?? 3),
                    'radius_2' => (float) ($data['radius_2'] ?? 5),
                    'radius_3' => (float) ($data['radius_3'] ?? 8),
                ];
            }
        }
        return [
            'name' => 'Pizza Happy Family (Main Branch)',
            'address' => 'Phnom Penh Central Kitchen',
            'lat' => 11.5564,
            'lng' => 104.9282,
            'phone' => '012 345 678',
            'radius_1' => 3,
            'radius_2' => 5,
            'radius_3' => 8,
        ];
    }

    /**
     * Update Main Office Location Settings
     */
    public function updateStoreLocation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'lat' => 'required|numeric|between:-90,90',
            'lng' => 'required|numeric|between:-180,180',
            'radius_1' => 'nullable|numeric|min:0.5',
            'radius_2' => 'nullable|numeric|min:1',
            'radius_3' => 'nullable|numeric|min:2',
        ]);

        $file = storage_path('app/store_settings.json');
        if (!file_exists(dirname($file))) {
            mkdir(dirname($file), 0755, true);
        }
        file_put_contents($file, json_encode($validated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'status' => 'ok',
                'message' => 'បានផ្លាស់ប្តូរទីតាំងហាងធំ (Main Office) ដោយជោគជ័យ!',
                'store' => [
                    'name' => $validated['name'],
                    'address' => $validated['address'] ?? '',
                    'phone' => $validated['phone'] ?? '',
                    'lat' => (float) $validated['lat'],
                    'lng' => (float) $validated['lng'],
                    'radius_1' => (float) ($validated['radius_1'] ?? 3),
                    'radius_2' => (float) ($validated['radius_2'] ?? 5),
                    'radius_3' => (float) ($validated['radius_3'] ?? 8),
                ],
            ]);
        }

        return redirect()->back()->with('success', 'បានផ្លាស់ប្តូរទីតាំងហាងធំ (Main Office) ដោយជោគជ័យ!');
    }

    /**
     * Fetch Road Driving Route from OSRM Engine (Server-side proxy with caching)
     */
    public function getRoadRoute(Request $request)
    {
        $request->validate([
            'from_lat' => 'required|numeric',
            'from_lng' => 'required|numeric',
            'to_lat' => 'required|numeric',
            'to_lng' => 'required|numeric',
        ]);

        $fromLat = (float) $request->from_lat;
        $fromLng = (float) $request->from_lng;
        $toLat = (float) $request->to_lat;
        $toLng = (float) $request->to_lng;

        $cacheKey = sprintf('osrm_route_%s_%s_%s_%s', round($fromLat, 4), round($fromLng, 4), round($toLat, 4), round($toLng, 4));

        $data = cache()->remember($cacheKey, 86400, function () use ($fromLat, $fromLng, $toLat, $toLng) {
            $url = sprintf(
                'https://router.project-osrm.org/route/v1/driving/%F,%F;%F,%F?overview=full&geometries=geojson',
                $fromLng,
                $fromLat,
                $toLng,
                $toLat
            );

            try {
                $response = \Illuminate\Support\Facades\Http::timeout(5)
                    ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; MyPizzaDelivery/1.0)'])
                    ->get($url);

                if ($response->successful()) {
                    $json = $response->json();
                    if (($json['code'] ?? '') === 'Ok' && !empty($json['routes'][0])) {
                        $route = $json['routes'][0];
                        // Convert [lng, lat] to [lat, lng] for Leaflet
                        $coords = array_map(function ($pt) {
                            return [(float)$pt[1], (float)$pt[0]];
                        }, $route['geometry']['coordinates'] ?? []);

                        return [
                            'status' => 'ok',
                            'coordinates' => $coords,
                            'distance_km' => round($route['distance'] / 1000, 1),
                            'duration_mins' => max(1, round($route['duration'] / 60)),
                        ];
                    }
                }
            } catch (\Exception $e) {
                // Return null on failure
            }

            return null;
        });

        if ($data) {
            return response()->json($data);
        }

        // Fallback: direct 2 points line
        return response()->json([
            'status' => 'fallback',
            'coordinates' => [
                [$fromLat, $fromLng],
                [$toLat, $toLng],
            ],
            'distance_km' => null,
            'duration_mins' => null,
        ]);
    }
}
