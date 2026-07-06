<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\AgencyVendor;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class dashboard extends Controller
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
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

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
        $month = $request->input('month', Carbon::now()->month);
        $year = $request->input('year', Carbon::now()->year);

        $expenses = DB::table('expenses')
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
        $year = $request->input('year', Carbon::now()->year);

        $expenses = DB::table('expenses')
            ->whereYear('expense_date', $year)
            ->whereNull('deleted_at')
            ->select(
                DB::raw('MONTH(expense_date) as month'),
                DB::raw('SUM(amount) as total')
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
}
