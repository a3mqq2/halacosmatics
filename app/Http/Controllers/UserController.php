<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function index()
    {
        $users = User::latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'                   => $request->name,
            'phone'                  => $request->phone,
            'password'               => $request->password,
            'is_super'               => $request->boolean('is_super'),
            'perm_users'             => $request->boolean('perm_users'),
            'perm_orders_pending'    => $request->boolean('perm_orders_pending'),
            'perm_orders_active'     => $request->boolean('perm_orders_active'),
            'perm_orders_delivered'  => $request->boolean('perm_orders_delivered'),
            'perm_orders_returned'   => $request->boolean('perm_orders_returned'),
            'perm_orders_approve'    => $request->boolean('perm_orders_approve'),
            'perm_orders_deliver'    => $request->boolean('perm_orders_deliver'),
            'perm_agents'            => $request->boolean('perm_agents'),
            'perm_vaults'            => $request->boolean('perm_vaults'),
            'perm_products_view'     => $request->boolean('perm_products_view'),
            'perm_products_prices'   => $request->boolean('perm_products_prices'),
            'perm_products_costs'    => $request->boolean('perm_products_costs'),
            'perm_products_edit'     => $request->boolean('perm_products_edit'),
            'perm_products_stock'    => $request->boolean('perm_products_stock'),
            'perm_marketers_view'    => $request->boolean('perm_marketers_view'),
            'perm_marketers_manage'  => $request->boolean('perm_marketers_manage'),
            'perm_marketers_finance' => $request->boolean('perm_marketers_finance'),
            'perm_reports'           => $request->boolean('perm_reports'),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'تم إضافة المستخدم بنجاح.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'phone'    => 'required|string|max:20|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = [
            'name'                   => $request->name,
            'phone'                  => $request->phone,
            'is_super'               => $request->boolean('is_super'),
            'perm_users'             => $request->boolean('perm_users'),
            'perm_orders_pending'    => $request->boolean('perm_orders_pending'),
            'perm_orders_active'     => $request->boolean('perm_orders_active'),
            'perm_orders_delivered'  => $request->boolean('perm_orders_delivered'),
            'perm_orders_returned'   => $request->boolean('perm_orders_returned'),
            'perm_orders_approve'    => $request->boolean('perm_orders_approve'),
            'perm_orders_deliver'    => $request->boolean('perm_orders_deliver'),
            'perm_agents'            => $request->boolean('perm_agents'),
            'perm_vaults'            => $request->boolean('perm_vaults'),
            'perm_products_view'     => $request->boolean('perm_products_view'),
            'perm_products_prices'   => $request->boolean('perm_products_prices'),
            'perm_products_costs'    => $request->boolean('perm_products_costs'),
            'perm_products_edit'     => $request->boolean('perm_products_edit'),
            'perm_products_stock'    => $request->boolean('perm_products_stock'),
            'perm_marketers_view'    => $request->boolean('perm_marketers_view'),
            'perm_marketers_manage'  => $request->boolean('perm_marketers_manage'),
            'perm_marketers_finance' => $request->boolean('perm_marketers_finance'),
            'perm_reports'           => $request->boolean('perm_reports'),
        ];

        if ($request->filled('password')) {
            $data['password'] = $request->password;
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'تم تحديث بيانات المستخدم بنجاح.');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return back()->with('error', 'لا يمكنك حذف حسابك الخاص.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'تم حذف المستخدم بنجاح.');
    }
}
