@extends('layouts.admin')

@section('title', 'QR Code Details')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $qrCode->name }}</h1>
                    <div class="space-x-2">
                        <a href="{{ route('admin.qr-codes.edit', $qrCode) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                            Edit
                        </a>
                        <a href="{{ route('admin.qr-codes.download', $qrCode) }}"
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md">
                            Download QR
                        </a>
                        <a href="{{ route('admin.qr-codes.index') }}"
                           class="text-gray-600 hover:text-gray-900 px-4 py-2">
                            ← Back
                        </a>
                    </div>
                </div>

                @if(session('success'))
                    <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- QR Code Info -->
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">QR Code Information</h3>
                            <dl class="space-y-3">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Store</dt>
                                    <dd class="text-sm text-gray-900">{{ $qrCode->store->name }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Reference Code</dt>
                                    <dd class="text-sm font-mono text-gray-900">{{ $qrCode->ref_code }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Pre-filled Question</dt>
                                    <dd class="text-sm text-gray-900">
                                        {{ $qrCode->question ?: 'No question set' }}
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Target URL</dt>
                                    <dd class="text-sm text-blue-600 break-all">
                                        <a href="{{ $qrCode->getQrUrl() }}" target="_blank" class="hover:underline">
                                            {{ $qrCode->getQrUrl() }}
                                        </a>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                                    <dd>
                                        <span class="px-2 py-1 text-xs rounded-full {{ $qrCode->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $qrCode->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Created</dt>
                                    <dd class="text-sm text-gray-900">{{ $qrCode->created_at->format('M d, Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Statistics -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4">Analytics</h3>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $stats['total_scans'] }}</div>
                                    <div class="text-sm text-gray-500">Total Scans</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $stats['unique_ips'] }}</div>
                                    <div class="text-sm text-gray-500">Unique Visitors</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-purple-600">{{ $stats['mobile_scans'] }}</div>
                                    <div class="text-sm text-gray-500">Mobile Scans</div>
                                </div>
                                <div class="text-center">
                                    <div class="text-2xl font-bold text-orange-600">{{ $stats['recent_scans'] }}</div>
                                    <div class="text-sm text-gray-500">Last 7 Days</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- QR Code Image -->
                    <div class="text-center">
                        <h3 class="text-lg font-semibold mb-4">QR Code Image</h3>
                        @if($qrCode->qr_code_image && Storage::disk('public')->exists($qrCode->qr_code_image))
                            <div class="bg-white p-8 rounded-lg border border-gray-200 inline-block">
                                @if(pathinfo($qrCode->qr_code_image, PATHINFO_EXTENSION) === 'svg')
                                    <!-- SVG embedding for better compatibility -->
                                    <div class="w-64 h-64 mx-auto flex items-center justify-center">
                                        {!! Storage::disk('public')->get($qrCode->qr_code_image) !!}
                                    </div>
                                @else
                                    <!-- Regular image -->
                                    <img src="{{ Storage::disk('public')->url($qrCode->qr_code_image) }}"
                                         alt="QR Code for {{ $qrCode->name }}"
                                         class="w-64 h-64 mx-auto"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div style="display:none;" class="w-64 h-64 mx-auto flex items-center justify-center bg-gray-100 border border-gray-300 rounded">
                                        <div class="text-center">
                                            <p class="text-gray-500 text-sm">Errore caricamento</p>
                                            <p class="text-gray-400 text-xs">Prova a rigenerare</p>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="mt-4 space-y-2">
                                <p class="text-sm text-gray-500">
                                    Scan with phone to test or right-click to save image
                                </p>
                                <div class="flex justify-center space-x-3">
                                    <a href="{{ route('admin.qr-codes.download', $qrCode) }}"
                                       class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                                        Download QR Code
                                    </a>
                                    <form method="POST" action="{{ route('admin.qr-codes.regenerate', $qrCode) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                                            Rigenera QR Code
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="bg-gray-100 p-8 rounded-lg border border-gray-200">
                                <p class="text-gray-500">QR Code image not generated yet</p>
                                <form method="POST" action="{{ route('admin.qr-codes.update', $qrCode) }}" class="mt-4">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="store_id" value="{{ $qrCode->store_id }}">
                                    <input type="hidden" name="name" value="{{ $qrCode->name }}">
                                    <input type="hidden" name="question" value="{{ $qrCode->question }}">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                                        Generate QR Code
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Recent Scans -->
                @if($qrCode->scans->count() > 0)
                    <div class="mt-8">
                        <h3 class="text-lg font-semibold mb-4">Recent Scans</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP Address</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Device</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($qrCode->scans()->latest()->take(10)->get() as $scan)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $scan->ip_address }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <span class="capitalize">{{ $scan->device_type }}</span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $scan->created_at->diffForHumans() }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
