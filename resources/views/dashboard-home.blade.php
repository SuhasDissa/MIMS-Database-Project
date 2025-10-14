<x-layouts.auth.clean>
    <div class="flex h-screen w-full items-center justify-center bg-gray-100 p-8">
        <div class="w-full max-w-4xl">
            <h1 class="text-5xl font-bold text-gray-800 mb-12 text-center">MIMS Dashboard</h1>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <a href="{{ route('create.customer') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 flex items-center space-x-4">
                    <div class="bg-blue-500 text-white p-3 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M15 21v-1a6 6 0 00-5.197-5.803"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Create Customer</h2>
                        <p class="text-gray-500">Add a new customer</p>
                    </div>
                </a>
                <a href="{{ route('create.fd') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 flex items-center space-x-4">
                    <div class="bg-green-500 text-white p-3 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Create FD</h2>
                        <p class="text-gray-500">Create a new Fixed Deposit</p>
                    </div>
                </a>
                <a href="{{ route('create.employee') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 flex items-center space-x-4">
                    <div class="bg-purple-500 text-white p-3 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Create Employee</h2>
                        <p class="text-gray-500">Add a new employee</p>
                    </div>
                </a>
                <a href="{{ route('create.branch') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 flex items-center space-x-4">
                    <div class="bg-yellow-500 text-white p-3 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Create Branch</h2>
                        <p class="text-gray-500">Add a new branch</p>
                    </div>
                </a>
                <a href="{{ route('sv.add') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 flex items-center space-x-4">
                    <div class="bg-red-500 text-white p-3 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Add Savings Acc Type</h2>
                        <p class="text-gray-500">Add a new savings account type</p>
                    </div>
                </a>
                <a href="{{ route('fd.add') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 flex items-center space-x-4">
                    <div class="bg-indigo-500 text-white p-3 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 8h6m-5 4h4m5 6H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Add FD Type</h2>
                        <p class="text-gray-500">Add a new FD type</p>
                    </div>
                </a>
                <a href="{{ route('create.transaction') }}" class="bg-white p-6 rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 flex items-center space-x-4">
                    <div class="bg-pink-500 text-white p-3 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h.01M12 7h.01M16 7h.01M9 17h6M9 12h6m-6-5h6m-6 10h6M3 7h18M3 12h18M3 17h18"></path></svg>
                    </div>
                    <div>
                        <h2 class="text-xl font-semibold text-gray-700">Create Transaction</h2>
                        <p class="text-gray-500">Create a new transaction</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</x-layouts.auth.clean>
