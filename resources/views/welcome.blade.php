<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to Our Bank</title>
    @include('partials.head')
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">
    <!-- Navigation -->
    <nav class="sticky top-0 z-50 bg-white/80 dark:bg-gray-900/80 backdrop-blur-lg border-b border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-2">
                    <x-app-logo class="h-15 w-auto" />
                </div>

                @if (Route::has('login'))
                    <div class="flex items-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-primary btn-sm">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-ghost btn-sm">
                                Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-primary btn-sm">
                                    Register
                                </a>
                            @endif
                        @endauth
                    </div>
                @endif
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="container mx-auto px-4 lg:px-8 py-16 lg:py-24">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8">
                <div class="space-y-4">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-primary/10 rounded-full text-primary text-sm font-medium">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                        Trusted by 50,000+ Customers
                    </div>
                    <h1 class="text-4xl lg:text-6xl font-bold text-gray-900 dark:text-white leading-tight">
                        Banking Made <span class="text-primary">Simple</span> & Secure
                    </h1>
                    <p class="text-lg text-gray-600 dark:text-gray-300">
                        Experience modern banking with our comprehensive financial solutions. From savings accounts to fixed deposits, we're here to help you achieve your financial goals.
                    </p>
                </div>

                <div class="flex flex-wrap gap-4">
                    @guest
                        <a href="{{ route('register') }}" class="btn btn-primary btn-lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Open Account
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                            Go to Dashboard
                        </a>
                    @endguest
                    <a href="#services" class="btn btn-outline btn-lg">
                        Learn More
                    </a>
                </div>

                <!-- Stats -->
                <div class="grid grid-cols-3 gap-6 pt-8 border-t border-gray-200 dark:border-gray-700">
                    <div>
                        <div class="text-3xl font-bold text-primary">50k+</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Customers</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-primary">Rs. 10B+</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Assets Under Management</div>
                    </div>
                    <div>
                        <div class="text-3xl font-bold text-primary">24/7</div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Support</div>
                    </div>
                </div>
            </div>

            <div class="relative">
                <!-- Round Logo Display -->
                <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 z-20">
                    <div class="bg-white dark:bg-gray-800 rounded-full p-8 shadow-2xl">
                        <x-app-logo-icon class="size-32 md:size-40" />
                    </div>
                </div>
                
                <div class="relative z-10 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-3xl p-8 backdrop-blur-sm border border-primary/20">
                    <img src="{{ asset('bank.jpg') }}" alt="Banking" class="rounded-2xl shadow-2xl w-full h-auto opacity-60" onerror="this.style.display='none'">
                    <div class="absolute inset-0 bg-gradient-to-t from-primary/30 to-transparent rounded-3xl"></div>
                </div>
                <div class="absolute -bottom-6 -left-6 w-48 h-48 bg-primary/10 rounded-full blur-3xl"></div>
                <div class="absolute -top-6 -right-6 w-48 h-48 bg-secondary/10 rounded-full blur-3xl"></div>
            </div>
        </div>
    </section>

    <!-- Exchange Rates Section -->
    <section class="bg-gradient-to-r from-primary/5 to-secondary/5 py-16">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white mb-4">
                    Live Exchange Rates
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300">
                    Current exchange rates for Sri Lankan Rupee (LKR)
                </p>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                <!-- USD -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 dark:text-blue-400 font-bold">$</span>
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">USD</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">318.52</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">US Dollar</div>
                </div>

                <!-- GBP -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900/50 rounded-full flex items-center justify-center">
                            <span class="text-indigo-600 dark:text-indigo-400 font-bold">£</span>
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">GBP</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">390.15</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">British Pound</div>
                </div>

                <!-- INR -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                            <span class="text-green-600 dark:text-green-400 font-bold">₹</span>
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">INR</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">3.82</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Indian Rupee</div>
                </div>

                <!-- JPY -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-red-100 dark:bg-red-900/50 rounded-full flex items-center justify-center">
                            <span class="text-red-600 dark:text-red-400 font-bold">¥</span>
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">JPY</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">2.11</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Japanese Yen</div>
                </div>

                <!-- KWD -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-full flex items-center justify-center">
                            <span class="text-purple-600 dark:text-purple-400 font-bold">د.ك</span>
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">KWD</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">1032.45</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Kuwaiti Dinar</div>
                </div>

                <!-- SAR -->
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-lg border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-10 h-10 bg-yellow-100 dark:bg-yellow-900/50 rounded-full flex items-center justify-center">
                            <span class="text-yellow-600 dark:text-yellow-400 font-bold">﷼</span>
                        </div>
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">SAR</span>
                    </div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">84.93</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Saudi Riyal</div>
                </div>
            </div>

            <div class="text-center mt-8 text-sm text-gray-500 dark:text-gray-400">
                <p>Exchange rates are updated daily. Last updated: {{ now()->format('M d, Y H:i') }}</p>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="services" class="bg-white dark:bg-gray-800 py-16 lg:py-24">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 dark:text-white mb-4">
                    Our Services
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-300 max-w-2xl mx-auto">
                    Comprehensive banking solutions tailored to your needs
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Savings Account -->
                <div class="group bg-base-100 dark:bg-gray-900 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-primary hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-primary/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Savings Account</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Secure savings with competitive interest rates and easy access to your funds.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            High interest rates
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            No minimum balance
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Free online banking
                        </li>
                    </ul>
                </div>

                <!-- Fixed Deposits -->
                <div class="group bg-base-100 dark:bg-gray-900 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-primary hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-secondary/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Fixed Deposits</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Higher returns with flexible tenures and guaranteed interest rates.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Premium interest rates
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Flexible tenures
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Guaranteed returns
                        </li>
                    </ul>
                </div>

                <!-- Online Banking -->
                <div class="group bg-base-100 dark:bg-gray-900 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-primary hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-accent/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Online Banking</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Manage your accounts anytime, anywhere with our secure platform.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            24/7 access
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Secure transactions
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Instant transfers
                        </li>
                    </ul>
                </div>

                <!-- Branch Network -->
                <div class="group bg-base-100 dark:bg-gray-900 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-primary hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-info/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-info" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Branch Network</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Visit any of our branches for personalized banking services.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Nationwide presence
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Expert staff
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Extended hours
                        </li>
                    </ul>
                </div>

                <!-- Customer Support -->
                <div class="group bg-base-100 dark:bg-gray-900 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-primary hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-warning/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Customer Support</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Dedicated support team available round the clock for your queries.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            24/7 helpline
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Live chat
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Email support
                        </li>
                    </ul>
                </div>

                <!-- Secure Banking -->
                <div class="group bg-base-100 dark:bg-gray-900 p-8 rounded-2xl border border-gray-200 dark:border-gray-700 hover:border-primary hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 bg-success/10 rounded-xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg class="w-7 h-7 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold mb-3 dark:text-white">Secure Banking</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        Bank-grade security with encryption and fraud protection.
                    </p>
                    <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            256-bit encryption
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Two-factor auth
                        </li>
                        <li class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-success" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            Fraud monitoring
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="container mx-auto px-4 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <div class="flex items-center space-x-2 mb-4">
                        <x-app-logo class="h-32 w-auto text-white" />
                    </div>
                    <p class="text-gray-400 text-sm">
                        Your trusted partner for all banking needs
                    </p>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Services</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">Savings Account</a></li>
                        <li><a href="#" class="hover:text-white">Fixed Deposits</a></li>
                        <li><a href="#" class="hover:text-white">Online Banking</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Company</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">About Us</a></li>
                        <li><a href="#" class="hover:text-white">Careers</a></li>
                        <li><a href="#" class="hover:text-white">Contact</a></li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold mb-4">Legal</h3>
                    <ul class="space-y-2 text-sm text-gray-400">
                        <li><a href="#" class="hover:text-white">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-white">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-white">Security</a></li>
                    </ul>
                </div>
            </div>
            <div class="border-t border-gray-800 mt-8 pt-8 text-center text-sm text-gray-400">
                <p>&copy; {{ date('Y') }} Trust Bank. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
