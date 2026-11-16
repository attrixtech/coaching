@extends('layouts.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">Fee Management & Installment Dues</h1>
            <p class="text-xs text-slate-500 mt-1">Track scheduled course installments, outstanding receivables, and collections.</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('fees.payments') }}" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 text-slate-700 px-4 py-2 rounded-xl text-xs font-semibold transition">
                <i class="fa-solid fa-receipt"></i>
                <span>Receipts Register</span>
            </a>
            <a href="{{ route('fees.collect') }}" class="inline-flex items-center gap-2 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-xl text-xs font-semibold shadow-md shadow-brand-600/30 transition">
                <i class="fa-solid fa-cash-register"></i>
                <span>Cashier Collection Desk</span>
            </a>
        </div>
    </div>

    <!-- 3 Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Contract Dues</span>
            <div class="text-2xl font-bold text-slate-900 mt-1">₹{{ number_format($totalDuesAmount, 2) }}</div>
            <span class="text-xs text-slate-400">Total invoiced installments</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Fee Collected</span>
            <div class="text-2xl font-bold text-emerald-600 mt-1">₹{{ number_format($totalCollected, 2) }}</div>
            <span class="text-xs text-emerald-600 font-medium">50 settled payments</span>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs">
            <span class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Outstanding Balance</span>
            <div class="text-2xl font-bold text-rose-600 mt-1">₹{{ number_format($totalOutstanding, 2) }}</div>
            <span class="text-xs text-rose-500 font-medium">Pending collection</span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-xs">
        <form method="GET" action="{{ route('fees.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search student name, phone, admission..."
                       class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
            </div>

            <div>
                <select name="status" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <option value="">All Statuses</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid Only</option>
                    <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>Partially Paid</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                </select>
            </div>

            <div>
                <select name="branch_id" class="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 focus:bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    <option value="">All Campuses</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold py-2 rounded-xl transition">
                    Filter
                </button>
                <a href="{{ route('fees.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl text-xs transition">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Dues Table -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Student / Roll</th>
                        <th class="px-4 py-3 font-semibold">Installment Title</th>
                        <th class="px-4 py-3 font-semibold">Due Date</th>
                        <th class="px-4 py-3 font-semibold">Net Due</th>
                        <th class="px-4 py-3 font-semibold">Paid Amount</th>
                        <th class="px-4 py-3 font-semibold">Balance</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-slate-700">
                    @forelse($dues as $due)
                    <tr class="hover:bg-slate-50/70 transition">
                        <td class="px-4 py-3">
                            <div class="font-bold text-slate-900">{{ $due->student->full_name ?? 'Candidate' }}</div>
                            <div class="text-[11px] text-slate-400 font-mono">{{ $due->student->admission_no ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-800">{{ $due->title }}</div>
                            <span class="text-[10px] text-slate-400">Installment #{{ $due->installment_no }}</span>
                        </td>
                        <td class="px-4 py-3 font-mono text-[11px] text-slate-600">
                            {{ date('d M Y', strtotime($due->due_date)) }}
                        </td>
                        <td class="px-4 py-3 font-bold text-slate-900">₹{{ number_format($due->net_amount, 2) }}</td>
                        <td class="px-4 py-3 font-bold text-emerald-600">₹{{ number_format($due->paid_amount, 2) }}</td>
                        <td class="px-4 py-3 font-bold {{ $due->balance_amount > 0 ? 'text-rose-600' : 'text-slate-400' }}">
                            ₹{{ number_format($due->balance_amount, 2) }}
                        </td>
                        <td class="px-4 py-3">
                            @php
                                $badgeClass = match($due->status) {
                                    'paid' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'partially_paid' => 'bg-amber-50 text-amber-700 border-amber-200',
                                    default => 'bg-rose-50 text-rose-700 border-rose-200'
                                };
                            @endphp
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold border {{ $badgeClass }} uppercase">
                                {{ str_replace('_', ' ', $due->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if($due->status !== 'paid')
                                <a href="{{ route('fees.collect', ['due_id' => $due->id]) }}" class="inline-flex items-center gap-1 px-3 py-1 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold transition text-[11px]">
                                    <i class="fa-solid fa-cash-register text-[10px]"></i>
                                    <span>Collect</span>
                                </a>
                            @else
                                @if($due->payments->first())
                                    <a href="{{ route('fees.receipt', $due->payments->first()) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg font-semibold transition text-[11px]">
                                        <i class="fa-solid fa-receipt text-[10px]"></i>
                                        <span>Receipt</span>
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                            No fee records found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 bg-slate-50/50">
            {{ $dues->links() }}
        </div>
    </div>

</div>
@endsection

