<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Models\Category;
use App\Models\Expense;
use App\Models\Role;
use App\Models\User;
use App\Models\AgencyVendor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Display the dashboard.
    public function dashboard()
    {
        $todayExpense = Expense::query()->whereDate('expense_date', today())->sum('amount');
        $monthExpense = Expense::query()->whereYear('expense_date', now()->year)
                            ->whereMonth('expense_date', now()->month)
                            ->sum('amount');
        $yearExpense = Expense::query()->whereYear('expense_date', now()->year)->sum('amount');
        $totalBalance = AgencyVendor::query()->sum('balance');

        return view('auth.dashboard', [
            'user' => Auth::user(),
            'todayExpense' => $todayExpense,
            'monthExpense' => $monthExpense,
            'yearExpense' => $yearExpense,
            'totalBalance' => $totalBalance,
        ]);
    }

    public function getChartData(Request $request)
    {
        $month = $request->input('month', \Carbon\Carbon::now()->month);
        $year = $request->input('year', \Carbon\Carbon::now()->year);

        $expenses = Expense::whereYear('expense_date', $year)
            ->whereMonth('expense_date', $month)
            ->selectRaw('agency_vendor_id, SUM(amount) as total')
            ->groupBy('agency_vendor_id')
            ->get();

        $vendorIds = $expenses->pluck('agency_vendor_id');
        $vendors = AgencyVendor::whereIn('id', $vendorIds)->get()->keyBy('id'); 

        $labels = [];
        $data = [];

        foreach ($expenses as $expense) {
            $vendorName = isset($vendors[$expense->agency_vendor_id]) ? $vendors[$expense->agency_vendor_id]->name : 'Unknown';
            $labels[] = $vendorName;
            $data[] = $expense->total;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    public function getStackedChartData(Request $request)
    {
        $month = $request->input('month', \Carbon\Carbon::now()->month);
        $year = $request->input('year', \Carbon\Carbon::now()->year);

        $expenses = \Illuminate\Support\Facades\DB::table('expenses')
            ->leftJoin('categories', 'expenses.expense_category_id', '=', 'categories.id')
            ->leftJoin('sub_categories', 'expenses.expense_sub_category_id', '=', 'sub_categories.id')
            ->whereYear('expenses.expense_date', $year)
            ->whereMonth('expenses.expense_date', $month)
            ->whereNull('expenses.deleted_at')
            ->select([
                'categories.name as category_name',
                'sub_categories.name as sub_category_name',
                DB::raw('SUM(expenses.amount) as total')
            ])
            ->groupBy('category_name', 'sub_category_name')
            ->get();

        $categories = $expenses->pluck('category_name')->unique()->values()->all();
        $subCategories = $expenses->pluck('sub_category_name')->unique()->values()->all();

        $datasets = [];
        foreach ($subCategories as $subCat) {
            $data = [];
            foreach ($categories as $cat) {
                $match = $expenses->first(function ($item) use ($cat, $subCat) {
                    return $item->category_name === $cat && $item->sub_category_name === $subCat;
                });
                $data[] = $match ? (float) $match->total : 0;
            }
            $datasets[] = [
                'label' => $subCat,
                'data' => $data,
            ];
        }

        return response()->json([
            'labels' => $categories,
            'datasets' => $datasets,
        ]);
    }

    public function getLineChartData(Request $request)
    {
        $year = $request->input('year', \Carbon\Carbon::now()->year);

        $expenses = \Illuminate\Support\Facades\DB::table('expenses')
            ->whereYear('expense_date', $year)
            ->whereNull('deleted_at')
            ->select(
                \Illuminate\Support\Facades\DB::raw('MONTH(expense_date) as month'),
                \Illuminate\Support\Facades\DB::raw('SUM(amount) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        $labels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $data = [];

        for ($i = 1; $i <= 12; $i++) {
            $data[] = isset($expenses[$i]) ? (float) $expenses[$i]->total : 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $data,
        ]);
    }

    // Display the paginated user list.
    public function index(Request $request)
    {
        $search  = $request->input('search', '');
        $perPage = 8;

        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);

        $roles    = Role::orderBy('name')->get();
        $rolesMap = $roles->keyBy('name');

        return view('employees.index', [
            'user'     => Auth::user(),
            'users'    => $users,
            'search'   => $search,
            'rolesMap' => $rolesMap,
        ]);
    }

    // Store a newly created user.
    public function store(UpdateUserRequest $request)
    {
        $data = [
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => 'User', // default
        ];

        // Admin / User with edit-role may set role on creation
        if (Auth::user()->hasPermission('edit-role') && $request->filled('role')) {
            $data['role'] = $request->role;
        }

        User::create($data);

        return redirect()->route('users.index')
                         ->with('success', 'User created successfully.');
    }

    //Show the form for creating a new user.
     
    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('employees.create_user', [
            'user'     => Auth::user(),
            'roles'    => $roles,
            'rolesMap' => $roles->keyBy('name'),
        ]);
    }

    //Show the form for editing the specified user.
     
     
    public function edit(User $user)
    {
        $authUser = Auth::user();

        $roles = Role::orderBy('name')->get();

        return view('employees.edit_user', [
            'user'     => $authUser,
            'editUser' => $user,
            'roles'    => $roles,
            'rolesMap' => $roles->keyBy('name'),
        ]);
    }


    // Update an existing user.
    public function update(UpdateUserRequest $request, User $user)
    {
        $authUser = Auth::user();

        $data = [
            'name'  => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        // Only users with edit-role permission can change a user's role
        if ($authUser->hasPermission('edit-role') && $request->filled('role')) {
            // Prevent admin from demoting themselves
            if ($user->id === $authUser->id) {
                if (strtolower($request->role) !== 'admin') {
                    return redirect()->route('users.index')
                                     ->with('error', 'You cannot remove admin access from your own account.');
                }
            }
            $data['role'] = $request->role;
        }

        $user->update($data);

        return redirect()->route('users.index')
                         ->with('success', 'User updated successfully.');
    }

    // Delete a user.
    public function destroy(User $user)
    {
        $authUser = Auth::user();

        if ($user->id === $authUser->id) {
            return redirect()->route('users.index')
                             ->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User deleted successfully.');
    }
}
