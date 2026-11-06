@extends('layouts.admin')

@section('title', 'Customers')

@section('content')
<x-admin.page-header title="Customers" description="Shop accounts registered on the website, and everyone who has sent an enquiry." />

<form method="GET" class="relative mt-8 w-full sm:w-80">
    <label for="q" class="sr-only">Search customers</label>
    <x-icon name="search" class="pointer-events-none absolute top-1/2 left-3.5 -translate-y-1/2 text-smoke" />
    <input id="q" name="q" type="search" value="{{ $search }}" placeholder="Search name, email or phone…" class="admin-input pl-10">
</form>

<h3 class="mt-8 font-semibold">Shop accounts <span class="font-normal text-smoke">{{ $accounts->total() }}</span></h3>
<section class="mt-3 overflow-hidden rounded-2xl bg-white ring-1 ring-ink/[0.06]">
    @if ($accounts->isEmpty())
        <div class="px-6 py-14 text-center">
            <x-icon name="user-round" class="text-3xl text-brass" />
            <p class="mt-4 font-semibold">{{ $search ? 'No accounts match your search.' : 'No shop accounts yet.' }}</p>
            <p class="mt-1 text-sm text-smoke">Customers appear here after creating an account on the website.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead class="border-b border-ink/5 bg-[#faf8f4] text-left text-xs text-smoke">
                    <tr>
                        <th class="px-5 py-3 font-medium lg:px-7">Name</th>
                        <th class="px-3 py-3 font-medium">Contact</th>
                        <th class="px-3 py-3 text-right font-medium">Orders</th>
                        <th class="px-3 py-3 text-right font-medium">Spent</th>
                        <th class="px-5 py-3 text-right font-medium lg:px-7">Joined</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5">
                    @foreach ($accounts as $account)
                        <tr class="transition hover:bg-[#faf8f4]">
                            <td class="px-5 py-3.5 font-semibold lg:px-7">{{ $account->name }}</td>
                            <td class="px-3 py-3.5">
                                <a href="mailto:{{ $account->email }}" class="hover:underline">{{ $account->email }}</a>
                                @if ($account->phone)<p class="text-xs text-smoke">{{ $account->phone }}</p>@endif
                            </td>
                            <td class="px-3 py-3.5 text-right tabular-nums">
                                @if ($account->orders_count)
                                    <a href="{{ route('admin.orders.index', ['q' => $account->email]) }}" class="hover:underline">{{ $account->orders_count }}</a>
                                @else
                                    0
                                @endif
                            </td>
                            <td class="px-3 py-3.5 text-right tabular-nums">{{ \App\Models\Order::money((int) $account->spent) }}</td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap text-smoke lg:px-7">{{ $account->created_at->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

{{ $accounts->links('admin.partials.pagination') }}

<h3 class="mt-10 font-semibold">Enquiry contacts <span class="font-normal text-smoke">{{ $customers->total() }}</span></h3>
<section class="mt-3 overflow-hidden rounded-2xl bg-white ring-1 ring-ink/[0.06]">
    @if ($customers->isEmpty())
        <div class="px-6 py-20 text-center">
            <x-icon name="users" class="text-3xl text-brass" />
            <p class="mt-4 font-semibold">{{ $search ? 'No customers match your search.' : 'No customers yet.' }}</p>
            <p class="mt-1 text-sm text-smoke">People appear here after sending an enquiry from the website.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full min-w-[640px] text-sm">
                <thead class="border-b border-ink/5 bg-[#faf8f4] text-left text-xs text-smoke">
                    <tr>
                        <th class="px-5 py-3 font-medium lg:px-7">Name</th>
                        <th class="px-3 py-3 font-medium">Contact</th>
                        <th class="px-3 py-3 text-right font-medium">Enquiries</th>
                        <th class="px-5 py-3 text-right font-medium lg:px-7">Last enquiry</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-ink/5">
                    @foreach ($customers as $customer)
                        <tr class="transition hover:bg-[#faf8f4]">
                            <td class="px-5 py-3.5 font-semibold lg:px-7">{{ $customer->name }}</td>
                            <td class="px-3 py-3.5">
                                <a href="mailto:{{ $customer->email }}" class="hover:underline">{{ $customer->email }}</a>
                                @if ($customer->phone)<p class="text-xs text-smoke">{{ $customer->phone }}</p>@endif
                            </td>
                            <td class="px-3 py-3.5 text-right tabular-nums">
                                <a href="{{ route('admin.enquiries.index', ['q' => $customer->email]) }}" class="hover:underline">{{ $customer->enquiries }}</a>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap text-smoke lg:px-7">{{ \Illuminate\Support\Carbon::parse($customer->last_enquiry_at)->diffForHumans() }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</section>

{{ $customers->links('admin.partials.pagination') }}
@endsection
