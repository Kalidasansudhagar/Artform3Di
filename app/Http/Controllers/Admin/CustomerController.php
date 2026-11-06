<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Enquiry;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Registered shop accounts (with their orders), and everyone who has sent an
 * enquiry, grouped by email address.
 */
class CustomerController extends Controller
{
    public function __invoke(Request $request): View
    {
        $search = trim($request->string('q')->toString());

        $accounts = User::query()
            ->where('is_admin', false)
            ->withCount('orders')
            ->withSum('orders as spent', 'total')
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")))
            ->latest()
            ->paginate(20, pageName: 'accounts')
            ->withQueryString();

        $customers = Enquiry::query()
            ->selectRaw('email, MAX(name) as name, MAX(phone) as phone, COUNT(*) as enquiries, MAX(created_at) as last_enquiry_at')
            ->when($search !== '', fn ($q) => $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")))
            ->groupBy('email')
            ->orderByDesc('last_enquiry_at')
            ->paginate(20, pageName: 'contacts')
            ->withQueryString();

        return view('admin.customers.index', ['accounts' => $accounts, 'customers' => $customers, 'search' => $search]);
    }
}
