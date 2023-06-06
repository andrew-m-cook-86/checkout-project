<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Products') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <table style="width: 100%">
                    <thead>
                    <th class="p-6 text-left text-gray-900 dark:text-gray-100">Name</th>
                    <th class="p-6 text-left text-gray-900 dark:text-gray-100">Price</th>
                    </thead>
                    <tbody>
                    @foreach($products as $product)
                        <tr>
                            <td class="p-6 text-gray-900 dark:text-gray-100">{{ $product->name }}</td>
                            <td class="p-6 text-gray-900 dark:text-gray-100">{{ $product->currency->prefix . $product->price . ' ' . $product->currency->name }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <div class="p-6 text-white text-lg">
                    {{ $products->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
