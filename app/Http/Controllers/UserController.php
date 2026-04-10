<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,staff,staff_inventory',
            'profile' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $filename = 'user_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/users'), $filename);
            $validated['profile_image'] = 'storage/users/' . $filename;
        }

        User::create([
            ...$validated,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('users.index')->with('success', 'User created successfully');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $period = request('period', 'today');
        $date = request('date', now()->format('Y-m-d'));

        // Build date range based on period
        switch ($period) {
            case 'today':
                $start = Carbon::parse($date)->startOfDay();
                $end = Carbon::parse($date)->endOfDay();
                break;
            case 'week':
                $start = Carbon::parse($date)->startOfWeek();
                $end = Carbon::parse($date)->endOfWeek();
                break;
            case 'month':
                $start = Carbon::parse($date)->startOfMonth();
                $end = Carbon::parse($date)->endOfMonth();
                break;
            case 'year':
                $start = Carbon::parse($date)->startOfYear();
                $end = Carbon::parse($date)->endOfYear();
                break;
            default:
                $start = Carbon::today()->startOfDay();
                $end = Carbon::today()->endOfDay();
        }

        $ordersQuery = $user->orders()->whereBetween('order_date', [$start, $end]);

        $stats = [
            'total_orders' => (clone $ordersQuery)->count(),
            'completed_orders' => (clone $ordersQuery)->where('status', 'completed')->count(),
            'pending_orders' => (clone $ordersQuery)->where('status', 'pending')->count(),
            'cancelled_orders' => (clone $ordersQuery)->where('status', 'cancelled')->count(),
            'total_revenue' => (clone $ordersQuery)->where('status', '!=', 'cancelled')->sum('total_amount'),
            'paid_amount' => (clone $ordersQuery)->where('payment_status', 'paid')->sum('total_amount'),
            'unpaid_amount' => (clone $ordersQuery)->where('payment_status', 'unpaid')->sum('total_amount'),
        ];

        // Get orders list for the period
        $orders = (clone $ordersQuery)->with('customer', 'items.product')->latest('order_date')->paginate(10);

        // All-time stats
        $allTimeStats = [
            'total_orders' => $user->orders()->count(),
            'total_revenue' => $user->orders()->where('status', '!=', 'cancelled')->sum('total_amount'),
        ];

        return view('users.show', compact('user', 'stats', 'orders', 'allTimeStats', 'period', 'date', 'start', 'end'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,staff,staff_inventory',
            'profile' => 'nullable|string',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        if ($request->hasFile('profile_image')) {
            // Delete old profile image if it exists
            if ($user->profile_image && file_exists(public_path($user->profile_image))) {
                unlink(public_path($user->profile_image));
            }
            $file = $request->file('profile_image');
            $filename = 'user_' . time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('storage/users'), $filename);
            $validated['profile_image'] = 'storage/users/' . $filename;
        }

        $user->update([
            ...$validated,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('users.show', $user)->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted successfully');
    }
}
