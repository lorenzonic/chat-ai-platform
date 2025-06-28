@extends('layouts.admin')

@section('title', 'Configure Sites')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Configure Sites</h1>
            <p class="mt-2 text-gray-600">Configure e-commerce sites for trends analysis</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Add New Site Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Add New Site</h2>
                <form id="addSiteForm" method="POST" action="{{ route('admin.trends.sites.store') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700 mb-1">Site Name</label>
                            <input type="text" name="site_name" id="site_name" required
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="e.g., Vivaio Online">
                        </div>
                        <div>
                            <label for="site_url" class="block text-sm font-medium text-gray-700 mb-1">Site URL</label>
                            <input type="url" name="site_url" id="site_url" required
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="https://example.com">
                        </div>
                        <div>
                            <label for="site_description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <input type="text" name="site_description" id="site_description"
                                   class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="Brief description of the site">
                        </div>
                        <div>
                            <label for="site_specialty" class="block text-sm font-medium text-gray-700 mb-1">Specialty</label>
                            <select name="site_specialty" id="site_specialty"
                                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="general">General Plants</option>
                                <option value="indoor">Indoor Plants</option>
                                <option value="outdoor">Outdoor Plants</option>
                                <option value="rare">Rare Plants</option>
                                <option value="herbs">Herbs & Vegetables</option>
                                <option value="succulents">Succulents</option>
                                <option value="tools">Garden Tools</option>
                            </select>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center">
                        <input type="checkbox" name="is_popular" id="is_popular" value="1"
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <label for="is_popular" class="ml-2 text-sm text-gray-700">Mark as popular site</label>
                    </div>
                    <div class="mt-6">
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm font-medium">
                            Add Site
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Site Selection Form -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-semibold">Available Sites ({{ count($availableSites) }})</h2>
                    <button type="button" onclick="validateAllSites()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm font-medium">
                        Validate Sites
                    </button>
                </div>

                <form id="scrapingConfigForm" method="GET" action="{{ route('admin.trends.index') }}">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                        <!-- Sites Selection -->
                        <div class="lg:col-span-2">
                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input type="checkbox" id="selectAll" class="rounded border-gray-300 text-indigo-600 shadow-sm">
                                    <span class="ml-2 text-sm font-medium text-gray-700">Select All Sites</span>
                                </label>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($availableSites as $key => $site)
                                <div class="border rounded-lg p-4 {{ $site['popular'] ? 'bg-blue-50 border-blue-200' : 'bg-gray-50 border-gray-200' }}">
                                    <div class="flex items-start space-x-3">
                                        <input class="site-checkbox mt-1 rounded border-gray-300 text-indigo-600 shadow-sm"
                                               type="checkbox"
                                               name="sites[]"
                                               value="{{ $key }}"
                                               id="site_{{ $key }}"
                                               {{ $site['popular'] ? 'checked' : '' }}>
                                        <div class="flex-1">
                                            <label class="text-sm font-medium text-gray-900 cursor-pointer" for="site_{{ $key }}">
                                                {{ $site['name'] }}
                                                @if($site['popular'])
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ml-2">
                                                        Popular
                                                    </span>
                                                @endif
                                            </label>
                                            <p class="text-xs text-gray-600 mt-1">{{ $site['description'] }}</p>
                                            <div class="flex items-center justify-between mt-2">
                                                <span class="text-xs text-gray-500">{{ $site['specialty'] }}</span>
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <svg class="w-3 h-3 {{ $i <= $site['rating'] ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                        </svg>
                                                    @endfor
                                                    <span class="ml-1 text-xs text-gray-500">{{ $site['rating'] }}</span>
                                                </div>
                                            </div>
                                            <a href="{{ $site['url'] }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-900 mt-1 block">
                                                {{ parse_url($site['url'], PHP_URL_HOST) }}
                                            </a>
                                            @if(isset($site['custom']) && $site['custom'])
                                                <button type="button" onclick="deleteSite('{{ $key }}')" class="text-xs text-red-600 hover:text-red-900 mt-1">
                                                    Delete Site
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Configuration Panel -->
                        <div>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <h3 class="text-sm font-medium text-gray-900 mb-4">Analysis Configuration</h3>

                                <div class="space-y-4">
                                    <div>
                                        <label for="scraping_mode" class="block text-sm font-medium text-gray-700 mb-1">Scraping Mode</label>
                                        <select name="scraping_mode" id="scraping_mode" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="auto">Auto (Real → Simulation)</option>
                                            <option value="real">Real Scraping Only</option>
                                            <option value="simulation">Simulation Only</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label for="days" class="block text-sm font-medium text-gray-700 mb-1">Analysis Period</label>
                                        <select name="days" id="days" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                                            <option value="7">Last 7 days</option>
                                            <option value="30" selected>Last 30 days</option>
                                            <option value="90">Last 90 days</option>
                                        </select>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Recommended Categories</label>
                                        <div class="flex flex-wrap gap-1">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Indoor Plants</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">Rare Plants</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Herbs</span>
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Outdoor</span>
                                        </div>
                                    </div>

                                    <div class="pt-4">
                                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded text-sm font-medium">
                                            Start Analysis
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Quick Stats -->
                            <div class="bg-white border rounded-lg p-4 mt-4">
                                <h3 class="text-sm font-medium text-gray-900 mb-3">Quick Info</h3>
                                <div class="space-y-2 text-xs text-gray-600">
                                    <div>
                                        <strong>Top Sites (Pre-selected):</strong>
                                        <ul class="list-disc list-inside mt-1 space-y-1">
                                            @foreach($availableSites as $key => $site)
                                                @if($site['popular'])
                                                    <li>{{ $site['name'] }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                    <div>
                                        <strong>For Rare Plants:</strong> Floricoltura Quaiato, Bakker
                                    </div>
                                    <div>
                                        <strong>Budget-Friendly:</strong> Piante.it, Giardinaggio.it
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sites Validation Results -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <h2 class="text-lg font-semibold mb-4">Sites Validation Status</h2>
                <div id="validationResults">
                    <div class="text-center py-8">
                        <button type="button" onclick="validateAllSites()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded text-sm font-medium">
                            Start Site Validation
                        </button>
                        <p class="text-gray-500 mt-2 text-sm">Check site accessibility and robots.txt compliance</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.site-checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
});

// Update Select All when individual checkboxes change
document.querySelectorAll('.site-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const total = document.querySelectorAll('.site-checkbox').length;
        const checked = document.querySelectorAll('.site-checkbox:checked').length;
        document.getElementById('selectAll').checked = (total === checked);
        document.getElementById('selectAll').indeterminate = (checked > 0 && checked < total);
    });
});

// Initialize checkbox states
document.addEventListener('DOMContentLoaded', function() {
    const total = document.querySelectorAll('.site-checkbox').length;
    const checked = document.querySelectorAll('.site-checkbox:checked').length;
    document.getElementById('selectAll').checked = (total === checked);
    document.getElementById('selectAll').indeterminate = (checked > 0 && checked < total);
});

// Validate sites function
async function validateAllSites() {
    const resultsDiv = document.getElementById('validationResults');
    resultsDiv.innerHTML = `
        <div class="text-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600 mx-auto"></div>
            <p class="mt-2 text-gray-600">Validating sites...</p>
        </div>
    `;

    try {
        const response = await fetch('/api/validate-sites', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
        Object.entries(data).forEach(([key, site]) => {
            const statusClass = site.compliant ? 'green' : site.accessible ? 'yellow' : 'red';
            const statusText = site.compliant ? 'COMPLIANT' : site.accessible ? 'LIMITED' : 'UNAVAILABLE';

            html += `
                <div class="border rounded-lg p-4 ${statusClass === 'green' ? 'bg-green-50 border-green-200' : statusClass === 'yellow' ? 'bg-yellow-50 border-yellow-200' : 'bg-red-50 border-red-200'}">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-medium text-gray-900">${site.name}</h4>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium ${statusClass === 'green' ? 'bg-green-100 text-green-800' : statusClass === 'yellow' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800'}">
                            ${statusText}
                        </span>
                    </div>
                    <p class="text-xs text-gray-600 mb-2">${site.description}</p>
                    <div class="text-xs text-gray-600">
                        <div>Accessible: ${site.accessible ? '✅' : '❌'}</div>
                        <div>Robots.txt: ${site.robots_allowed ? '✅' : '❌'}</div>
                    </div>
                </div>
            `;
        });
        html += '</div>';

        resultsDiv.innerHTML = html;

    } catch (error) {
        resultsDiv.innerHTML = `
            <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-yellow-800">Validation error: ${error.message}</span>
                </div>
            </div>
        `;
    }
}

// Delete site function
async function deleteSite(key) {
    if (!confirm('Are you sure you want to delete this site?')) {
        return;
    }

    try {
        const response = await fetch(`/admin/trends/sites/${key}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.success) {
            location.reload(); // Reload page to update site list
        } else {
            alert('Error deleting site: ' + data.message);
        }
    } catch (error) {
        alert('Error deleting site: ' + error.message);
    }
}
</script>
@endsection
