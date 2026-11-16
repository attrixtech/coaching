<!DOCTYPE html>
<html lang="en" class="h-full bg-slate-900">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — Webotapp Coaching Institute ERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body class="h-full flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-100">
        <!-- Header -->
        <div class="bg-gradient-to-br from-indigo-700 via-indigo-800 to-slate-900 p-8 text-center text-white relative">
            <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center shadow-lg border border-white/20">
                <i class="fa-solid fa-graduation-cap text-3xl text-indigo-300"></i>
            </div>
            <h1 class="text-2xl font-bold tracking-tight">Webotapp Academy</h1>
            <p class="text-xs text-indigo-200 mt-1">Coaching Institute Management ERP</p>
        </div>

        <div class="p-8">
            <!-- Flash Errors -->
            @if($errors->any())
                <div class="mb-5 bg-rose-50 border border-rose-200 text-rose-700 text-xs rounded-xl p-3">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('info'))
                <div class="mb-5 bg-sky-50 border border-sky-200 text-sky-800 text-xs rounded-xl p-3">
                    {{ session('info') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Official Email Address</label>
                    <div class="relative">
                        <i class="fa-regular fa-envelope absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                        <input type="email" name="email" value="{{ old('email', 'admin@coaching.com') }}" required 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-600 focus:bg-white focus:outline-none transition">
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold text-slate-700">Password</label>
                        <span class="text-[11px] text-indigo-600 font-medium">Demo: password123</span>
                    </div>
                    <div class="relative">
                        <i class="fa-solid fa-lock absolute left-3.5 top-3.5 text-slate-400 text-sm"></i>
                        <input type="password" name="password" value="password123" required 
                               class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-300 rounded-xl text-sm focus:ring-2 focus:ring-indigo-600 focus:bg-white focus:outline-none transition">
                    </div>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 text-indigo-600 rounded border-slate-300 focus:ring-indigo-500">
                        <span class="text-xs text-slate-600">Remember session</span>
                    </label>
                </div>

                <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-md shadow-indigo-600/30 transition duration-150">
                    Sign In to Portal
                </button>
            </form>

            <!-- Quick Demo One-Click Sign-in -->
            <div class="mt-6 pt-6 border-t border-slate-100">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 text-center mb-3">
                    Fast Demo One-Click Sign-In
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <a href="{{ route('quick-login', 'admin') }}" class="p-2.5 rounded-lg border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-slate-700 transition">
                        <i class="fa-solid fa-shield-halved text-indigo-600"></i>
                        <div class="truncate">
                            <div class="font-semibold leading-tight">Super Admin</div>
                            <div class="text-[10px] text-slate-400">Full Access</div>
                        </div>
                    </a>

                    <a href="{{ route('quick-login', 'counsellor') }}" class="p-2.5 rounded-lg border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-slate-700 transition">
                        <i class="fa-solid fa-headset text-indigo-600"></i>
                        <div class="truncate">
                            <div class="font-semibold leading-tight">Counsellor</div>
                            <div class="text-[10px] text-slate-400">Leads CRM</div>
                        </div>
                    </a>

                    <a href="{{ route('quick-login', 'accountant') }}" class="p-2.5 rounded-lg border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-slate-700 transition">
                        <i class="fa-solid fa-calculator text-indigo-600"></i>
                        <div class="truncate">
                            <div class="font-semibold leading-tight">Accountant</div>
                            <div class="text-[10px] text-slate-400">Fee Cashier</div>
                        </div>
                    </a>

                    <a href="{{ route('quick-login', 'faculty') }}" class="p-2.5 rounded-lg border border-slate-200 hover:border-indigo-500 hover:bg-indigo-50/50 flex items-center gap-2 text-slate-700 transition">
                        <i class="fa-solid fa-chalkboard-user text-indigo-600"></i>
                        <div class="truncate">
                            <div class="font-semibold leading-tight">Faculty</div>
                            <div class="text-[10px] text-slate-400">Classes & Marks</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
