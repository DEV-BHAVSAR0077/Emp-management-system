<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;

class Navbar extends Component
{
    public $user;
    public $myRoleColor;
    public $dashTabActive;
    public $empTabActive;
    public $rolesTabActive;
    public $expensesTabActive;
    public $categoriesTabActive;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->user = Auth::user();

        // Get user role color
        $myRoleObj = Role::where('name', $this->user->role)->first();
        $this->myRoleColor = $myRoleObj?->color ?? '#374151';

        // Determine active tab based on route and request parameters
        $route = request()->route()->getName();
        $tab = request('tab', '');

        if (in_array($route, ['users.create', 'users.edit'])) {
            $this->empTabActive = true;
            $this->dashTabActive = false;
            $this->rolesTabActive = false;
            $this->expensesTabActive = false;
            $this->categoriesTabActive = false;
        } elseif (in_array($route, ['roles.create', 'roles.edit'])) {
            $this->rolesTabActive = true;
            $this->dashTabActive = false;
            $this->empTabActive = false;
            $this->expensesTabActive = false;
            $this->categoriesTabActive = false;
        } elseif (in_array($route, ['expenses.create', 'expenses.edit'])) {
            $this->expensesTabActive = true;
            $this->dashTabActive = false;
            $this->empTabActive = false;
            $this->rolesTabActive = false;
            $this->categoriesTabActive = false;
        } else {
            // Dashboard route logic
            $this->empTabActive = $tab === 'emp' || request()->has('search') || request()->has('page');
            $this->rolesTabActive = $tab === 'roles';
            $this->expensesTabActive = $tab === 'expenses' || request()->has('expense_search') || request()->has('expense_page');
            $this->categoriesTabActive = $tab === 'categories';
            $this->dashTabActive = !$this->empTabActive && !$this->rolesTabActive && !$this->expensesTabActive && !$this->categoriesTabActive;
        }
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navbar');
    }
}
