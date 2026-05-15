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
        $route = request()->route()->getName() ?? '';

        $this->empTabActive        = str_starts_with($route, 'users.');
        $this->rolesTabActive      = str_starts_with($route, 'roles.');
        $this->expensesTabActive   = str_starts_with($route, 'expenses.');
        $this->categoriesTabActive = str_starts_with($route, 'categories.');
        $this->dashTabActive       = $route === 'dashboard';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.navbar');
    }
}
