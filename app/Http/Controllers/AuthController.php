<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm(Request $request)
    {

        return view('auth.signin');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('username', 'password');

        // Find user by username
        $user = User::where('username', $request->username)->first();

        if (! $user) {
            return back()->withErrors([
                'username' => __('auth.failed'),
            ])->withInput($request->except('password'));
        }

        // Check password
        if (! Hash::check($request->password, $user->password)) {
            return back()->withErrors([
                'username' => __('auth.failed'),
            ])->withInput($request->except('password'));
        }

        // Log in the user
        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', __('auth.logged_out'));
    }

    public function profile()
    {
        $availableLinks = [
            'dashboard' => 'Dashboard',
            'sale.create' => 'Create Sale',
            'sale.index' => 'Sales History',
            'purchase.create' => 'Create Purchase',
            'purchase.index' => 'Purchase History',
            'manufacturing_orders.create' => 'Create Order',
            'manufacturing_orders.index' => 'Order History',
            'account.create' => 'Create Account',
            'receivings.index' => 'Receive Payments',
            'issue.index' => 'Issue Payments',
            'transfers.index' => 'Transfers',
            'adjustments.index' => 'Accounts Adjustment',
            'expenses.index' => 'Expenses',
            'products.index' => 'Products',
            'stock-adjustments.index' => 'Stock Adjustment',
            'reports.profit_loss' => 'Profit / Loss Report',
            'reportCashbook' => 'Daily Cashbook',
            'reportExpense' => 'Expenses Report',
            'reportActivity' => 'Activity Report',
        ];

        return view('profile.index', compact('availableLinks'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'username' => 'required|string|unique:users,username,' . Auth::id(),
            'password' => 'nullable|string|min:4',
        ]);

        $user = User::find(Auth::id());
        $user->username = $request->username;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();
        return back()->with('success', 'Profile updated successfully');
    }

    public function bookmarks()
    {
        $availableLinks = [
            'dashboard' => 'Dashboard',
            'sale.create' => 'Create Sale',
            'sale.index' => 'Sales History',
            'purchase.create' => 'Create Purchase',
            'purchase.index' => 'Purchase History',
            'manufacturing_orders.create' => 'Create Order',
            'manufacturing_orders.index' => 'Order History',
            'account.create' => 'Create Account',
            'receivings.index' => 'Receive Payments',
            'issue.index' => 'Issue Payments',
            'transfers.index' => 'Transfers',
            'adjustments.index' => 'Accounts Adjustment',
            'expenses.index' => 'Expenses',
            'products.index' => 'Products',
            'stock-adjustments.index' => 'Stock Adjustment',
            'reports.profit_loss' => 'Profit / Loss Report',
            'reportCashbook' => 'Daily Cashbook',
            'reportExpense' => 'Expenses Report',
            'reportActivity' => 'Activity Report',
        ];

        return view('settings.bookmarks', compact('availableLinks'));
    }

    public function updateBookmarks(Request $request)
    {
        $request->validate([
            'bookmarks' => 'nullable|array',
        ]);

        $user = User::find(Auth::id());
        $user->bookmarks = $request->bookmarks ?? [];
        $user->save();

        return back()->with('success', 'Bookmarks updated successfully');
    }
}
