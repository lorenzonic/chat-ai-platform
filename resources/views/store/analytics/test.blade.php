<x-store-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                📊 Analytics Dashboard - Vue Test
            </h2>
            @if(auth('store')->user()->is_premium)
                <span class="premium-badge">Premium Account</span>
            @endif
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div id="analytics-app">
                <analytics-dashboard-test></analytics-dashboard-test>
            </div>
        </div>
    </div>
</x-store-layout>

@push('styles')
<style>
/* Analytics specific styles */
.analytics-dashboard {
    min-height: calc(100vh - 200px);
}

.chart-container {
    position: relative;
    height: 300px;
}

.premium-badge {
    background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
}
</style>
@endpush
