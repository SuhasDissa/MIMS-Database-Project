<?php
// Demo data for orders
$orders = [
    [
        'id' => 1,
        'customer' => 'John Doe',
        'customer_avatar' => 'https://ui-avatars.com/api/?name=John+Doe',
        'date' => '2023-05-15',
        'status' => 'Completed',
        'status_color' => 'green',
        'amount' => '$120.00'
    ],
    [
        'id' => 2,
        'customer' => 'Jane Smith',
        'customer_avatar' => 'https://ui-avatars.com/api/?name=Jane+Smith',
        'date' => '2023-05-14',
        'status' => 'Pending',
        'status_color' => 'yellow',
        'amount' => '$85.50'
    ],
    [
        'id' => 3,
        'customer' => 'Robert Johnson',
        'customer_avatar' => 'https://ui-avatars.com/api/?name=Robert+Johnson',
        'date' => '2023-05-13',
        'status' => 'Failed',
        'status_color' => 'red',
        'amount' => '$250.00'
    ],
    [
        'id' => 4,
        'customer' => 'Emily Davis',
        'customer_avatar' => 'https://ui-avatars.com/api/?name=Emily+Davis',
        'date' => '2023-05-12',
        'status' => 'Completed',
        'status_color' => 'green',
        'amount' => '$75.25'
    ],
    [
        'id' => 5,
        'customer' => 'Michael Wilson',
        'customer_avatar' => 'https://ui-avatars.com/api/?name=Michael+Wilson',
        'date' => '2023-05-11',
        'status' => 'Processing',
        'status_color' => 'yellow',
        'amount' => '$320.00'
    ]
];
?>

<div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Customer
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Date
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Status
                </th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Amount
                </th>
                <th scope="col" class="relative px-6 py-3">
                    <span class="sr-only">Actions</span>
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach ($orders as $order)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center gap-3">
                            <img class="h-8 w-8 rounded-full" src="{{ $order['customer_avatar'] }}"
                                alt="{{ $order['customer'] }}">
                            <span class="text-sm text-gray-900">{{ $order['customer'] }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $order['date'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span
                            class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if ($order['status_color'] === 'green') bg-green-100 text-green-800 
                            @elseif($order['status_color'] === 'red') bg-red-100 text-red-800
                            @elseif($order['status_color'] === 'yellow') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ $order['status'] }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $order['amount'] }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button type="button" class="text-gray-400 hover:text-gray-500">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20"
                                fill="currentColor">
                                <path
                                    d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                            </svg>
                        </button>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Simple pagination -->
<div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
    <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-700">
                Showing
                <span class="font-medium">1</span>
                to
                <span class="font-medium">5</span>
                of
                <span class="font-medium">5</span>
                results
            </p>
        </div>
        <div>
            <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Previous</span>
                    <!-- Heroicon name: solid/chevron-left -->
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
                <a href="#" aria-current="page" class="z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                    1
                </a>
                <a href="#" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                    <span class="sr-only">Next</span>
                    <!-- Heroicon name: solid/chevron-right -->
                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                </a>
            </nav>
        </div>
    </div>
</div>
