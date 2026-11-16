@extends('layouts.app')

@section('content')
<div class="space-y-6" x-data="{ tab: 'branches' }">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl font-bold text-slate-900 tracking-tight">System Settings & Campus Administration</h1>
            <p class="text-xs text-slate-500 mt-1">Configure multi-campus tenancy, user RBAC permissions, and institute parameters.</p>
        </div>
    </div>

    <!-- TABS BAR -->
    <div class="flex items-center gap-2 border-b border-slate-200 text-xs font-semibold">
        <button @click="tab = 'branches'" 
                :class="tab === 'branches' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-3 transition flex items-center gap-2">
            <i class="fa-solid fa-code-branch"></i>
            <span>Campuses & Branches ({{ $branches->count() }})</span>
        </button>

        <button @click="tab = 'users'" 
                :class="tab === 'users' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-3 transition flex items-center gap-2">
            <i class="fa-solid fa-users-gear"></i>
            <span>Staff Users & Roles</span>
        </button>

        <button @click="tab = 'system'" 
                :class="tab === 'system' ? 'border-brand-600 text-brand-600 border-b-2 font-bold' : 'text-slate-500 hover:text-slate-800 border-b-2 border-transparent'"
                class="px-4 py-3 transition flex items-center gap-2">
            <i class="fa-solid fa-sliders"></i>
            <span>Master System Settings</span>
        </button>
    </div>

    <!-- TAB 1: BRANCHES -->
    <div x-show="tab === 'branches'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900">Configured Physical Campuses & Branches</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3 font-semibold">Campus Name</th>
                            <th class="px-4 py-3 font-semibold">Code</th>
                            <th class="px-4 py-3 font-semibold">Contact Info</th>
                            <th class="px-4 py-3 font-semibold">Location</th>
                            <th class="px-4 py-3 font-semibold">Main Campus</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($branches as $b)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3 font-bold text-slate-900">{{ $b->name }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-brand-700">{{ $b->code }}</td>
                            <td class="px-4 py-3">
                                <div>{{ $b->phone }}</div>
                                <div class="text-[11px] text-slate-400">{{ $b->email }}</div>
                            </td>
                            <td class="px-4 py-3 text-slate-600">{{ $b->city }}, {{ $b->state }}</td>
                            <td class="px-4 py-3">
                                @if($b->is_main)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Headquarters</span>
                                @else
                                    <span class="text-slate-400 text-[10px]">Branch</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Branch Form -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
            <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-brand-600"></i>
                <span>Add Physical Campus</span>
            </h2>

            <form action="{{ route('settings.branches.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <input type="hidden" name="institute_id" value="1">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Campus Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. South Campus - Koramangala" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Branch Code <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" required placeholder="e.g. BLR-SOUTH" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-mono">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Contact Phone</label>
                    <input type="text" name="phone" placeholder="+91 98765 00000" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">City</label>
                    <input type="text" name="city" value="Bengaluru" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>

                <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-semibold shadow-xs transition">
                    Register Branch
                </button>
            </form>
        </div>
    </div>

    <!-- TAB 2: STAFF USERS -->
    <div x-show="tab === 'users'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="p-5 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900">User Accounts & Roles</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="px-4 py-3 font-semibold">User Name</th>
                            <th class="px-4 py-3 font-semibold">Email</th>
                            <th class="px-4 py-3 font-semibold">Role</th>
                            <th class="px-4 py-3 font-semibold">Branch</th>
                            <th class="px-4 py-3 font-semibold">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @foreach($users as $u)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-900">{{ $u->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $u->designation ?? $u->user_type }}</div>
                            </td>
                            <td class="px-4 py-3 font-mono">{{ $u->email }}</td>
                            <td class="px-4 py-3 font-semibold text-brand-700 capitalize">
                                {{ $u->role->name ?? str_replace('_', ' ', $u->user_type) }}
                            </td>
                            <td class="px-4 py-3">{{ $u->branch->name ?? 'Indiranagar' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 text-emerald-700 uppercase">
                                    {{ $u->status }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Add Staff User Form -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6">
            <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                <i class="fa-solid fa-user-shield text-brand-600"></i>
                <span>Create Staff User</span>
            </h2>

            <form action="{{ route('settings.users.store') }}" method="POST" class="space-y-4 text-xs">
                @csrf
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Ramesh Chandra" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Email Address <span class="text-rose-500">*</span></label>
                    <input type="email" name="email" required placeholder="ramesh@webotapp.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Role Permission <span class="text-rose-500">*</span></label>
                    <select name="role_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                        @foreach($roles as $r)
                            <option value="{{ $r->id }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Assigned Campus <span class="text-rose-500">*</span></label>
                    <select name="branch_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Official Designation</label>
                    <input type="text" name="designation" placeholder="e.g. Academic Counsellor" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Account Password <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" required value="password123" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-mono">
                </div>

                <button type="submit" class="w-full py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-semibold shadow-xs transition">
                    Create User Credentials
                </button>
            </form>
        </div>
    </div>

    <!-- TAB 3: SYSTEM SETTINGS -->
    <div x-show="tab === 'system'" class="max-w-3xl bg-white rounded-2xl border border-slate-200/80 shadow-xs p-6 sm:p-8">
        <h2 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
            <i class="fa-solid fa-sliders text-brand-600"></i>
            <span>Institute Global Parameters</span>
        </h2>

        <form action="{{ route('settings.general.update') }}" method="POST" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Institute Display Name</label>
                    <input type="text" name="institute_name" value="{{ $settings['institute_name'] ?? 'Webotapp Academy' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold text-slate-800">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Currency Code & Symbol</label>
                    <input type="text" name="currency_symbol" value="{{ $settings['currency_symbol'] ?? '₹' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 font-bold">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">GST Tax Rate (%)</label>
                    <input type="number" name="default_tax_rate" value="{{ $settings['default_tax_rate'] ?? '18' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1">Minimum Attendance Threshold (%)</label>
                    <input type="number" name="attendance_threshold_percentage" value="{{ $settings['attendance_threshold_percentage'] ?? '75' }}" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                </div>
            </div>

            <div class="pt-4 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-xl font-semibold shadow-xs transition">
                    Save System Parameters
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

