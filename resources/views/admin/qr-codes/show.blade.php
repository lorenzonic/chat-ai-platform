@extends('layouts.admin')

@section('title', 'QR Code Details')

@section('content')
<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $qrCode->name }}</h1>
                    <div class="flex space-x-2">
                        <a href="{{ route('admin.qr-codes.edit', $qrCode) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </a>
                        <a href="{{ route('admin.qr-codes.download', $qrCode) }}"
                           class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download QR
                        </a>
                        <button onclick="shareQrCode('{{ $qrCode->id }}', '{{ $qrCode->name }}', '{{ route('store.chatbot', $qrCode->store->slug) }}?ref={{ $qrCode->ref_code }}')"
                                class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-md inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                            </svg>
                            Share
                        </button>
                        <button onclick="deleteQrCode('{{ $qrCode->id }}', '{{ $qrCode->name }}')"
                                class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                        <a href="{{ route('admin.qr-codes.index') }}"
                           class="text-gray-600 hover:text-gray-900 px-4 py-2 inline-flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back
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

                        <!-- Quick Share Section -->
                        <div class="bg-blue-50 p-4 rounded-lg">
                            <h3 class="text-lg font-semibold mb-4 text-blue-900">Quick Share</h3>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-blue-700 mb-1">Direct Link</label>
                                    <div class="flex">
                                        <input type="text"
                                               value="{{ route('store.chatbot', $qrCode->store->slug) }}?ref={{ $qrCode->ref_code }}"
                                               readonly
                                               class="flex-1 text-xs border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500"
                                               id="quickShareUrl">
                                        <button onclick="copyQuickUrl()"
                                                class="px-3 py-2 bg-blue-600 text-white text-xs rounded-r-md hover:bg-blue-700"
                                                id="quickCopyBtn">
                                            Copy
                                        </button>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <button onclick="shareQuickFacebook()"
                                            class="flex-1 bg-blue-700 text-white px-2 py-2 rounded text-xs hover:bg-blue-800 inline-flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                        </svg>
                                        Facebook
                                    </button>
                                    <button onclick="shareQuickTwitter()"
                                            class="flex-1 bg-blue-400 text-white px-2 py-2 rounded text-xs hover:bg-blue-500 inline-flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                        </svg>
                                        Twitter
                                    </button>
                                    <button onclick="shareQuickWhatsApp()"
                                            class="flex-1 bg-green-600 text-white px-2 py-2 rounded text-xs hover:bg-green-700 inline-flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                        </svg>
                                        WhatsApp
                                    </button>
                                    <button onclick="shareQuickEmail()"
                                            class="flex-1 bg-gray-600 text-white px-2 py-2 rounded text-xs hover:bg-gray-700 inline-flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                                        </svg>
                                        Email
                                    </button>
                                </div>
                            </div>
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

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.232 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Delete QR Code</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">
                    Are you sure you want to delete <strong id="deleteQrName"></strong>?
                    This action cannot be undone and all scan data will be lost.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300 mr-2">
                        Delete
                    </button>
                </form>
                <button id="cancelDelete" class="px-4 py-2 bg-gray-400 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<div id="shareModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2 text-center">Share QR Code</h3>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Direct Link</label>
                <div class="flex">
                    <input type="text" id="shareUrl" readonly class="flex-1 border-gray-300 rounded-l-md focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <button onclick="copyToClipboard()" class="px-3 py-2 bg-blue-600 text-white text-sm rounded-r-md hover:bg-blue-700">
                        Copy
                    </button>
                </div>
            </div>
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Share on Social Media</label>
                <div class="flex space-x-2">
                    <button onclick="shareOnFacebook()" class="flex-1 bg-blue-600 text-white px-3 py-2 rounded text-sm hover:bg-blue-700">
                        Facebook
                    </button>
                    <button onclick="shareOnTwitter()" class="flex-1 bg-blue-400 text-white px-3 py-2 rounded text-sm hover:bg-blue-500">
                        Twitter
                    </button>
                    <button onclick="shareOnWhatsApp()" class="flex-1 bg-green-600 text-white px-3 py-2 rounded text-sm hover:bg-green-700">
                        WhatsApp
                    </button>
                </div>
            </div>
            <div class="mt-4 text-center">
                <button id="closeShare" class="px-4 py-2 bg-gray-400 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-500">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentShareUrl = '';
let currentShareTitle = '';

function deleteQrCode(qrCodeId, qrCodeName) {
    document.getElementById('deleteQrName').textContent = qrCodeName;
    document.getElementById('deleteForm').action = '/admin/qr-codes/' + qrCodeId;
    document.getElementById('deleteModal').classList.remove('hidden');
}

function shareQrCode(qrCodeId, qrCodeName, qrCodeUrl) {
    currentShareUrl = qrCodeUrl;
    currentShareTitle = 'Check out this QR Code: ' + qrCodeName;
    document.getElementById('shareUrl').value = qrCodeUrl;
    document.getElementById('shareModal').classList.remove('hidden');
}

function copyToClipboard() {
    const shareUrlInput = document.getElementById('shareUrl');
    shareUrlInput.select();
    shareUrlInput.setSelectionRange(0, 99999);
    document.execCommand('copy');

    // Show feedback
    const button = event.target;
    const originalText = button.textContent;
    button.textContent = 'Copied!';
    button.classList.add('bg-green-600');
    button.classList.remove('bg-blue-600');

    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-blue-600');
    }, 2000);
}

function shareOnFacebook() {
    const url = encodeURIComponent(currentShareUrl);
    const title = encodeURIComponent(currentShareTitle);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(currentShareUrl);
    const title = encodeURIComponent(currentShareTitle);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank', 'width=600,height=400');
}

function shareOnWhatsApp() {
    const url = encodeURIComponent(currentShareUrl);
    const title = encodeURIComponent(currentShareTitle);
    window.open(`https://wa.me/?text=${title} ${url}`, '_blank');
}

// Quick share functions
function copyQuickUrl() {
    const shareUrlInput = document.getElementById('quickShareUrl');
    shareUrlInput.select();
    shareUrlInput.setSelectionRange(0, 99999);
    document.execCommand('copy');

    // Show feedback
    const button = document.getElementById('quickCopyBtn');
    const originalText = button.textContent;
    button.textContent = 'Copied!';
    button.classList.add('bg-green-600');
    button.classList.remove('bg-blue-600');

    setTimeout(() => {
        button.textContent = originalText;
        button.classList.remove('bg-green-600');
        button.classList.add('bg-blue-600');
    }, 2000);
}

function shareQuickFacebook() {
    const url = encodeURIComponent(document.getElementById('quickShareUrl').value);
    const title = encodeURIComponent('Check out this QR Code: {{ $qrCode->name }}');
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}&quote=${title}`, '_blank', 'width=600,height=400');
}

function shareQuickTwitter() {
    const url = encodeURIComponent(document.getElementById('quickShareUrl').value);
    const title = encodeURIComponent('Check out this QR Code: {{ $qrCode->name }}');
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank', 'width=600,height=400');
}

function shareQuickWhatsApp() {
    const url = encodeURIComponent(document.getElementById('quickShareUrl').value);
    const title = encodeURIComponent('Check out this QR Code: {{ $qrCode->name }}');
    window.open(`https://wa.me/?text=${title} ${url}`, '_blank');
}

function shareQuickEmail() {
    const url = encodeURIComponent(document.getElementById('quickShareUrl').value);
    const subject = encodeURIComponent('Check out this QR Code: {{ $qrCode->name }}');
    const body = encodeURIComponent(`Hi,\n\nI wanted to share this QR Code with you: {{ $qrCode->name }}\n\nYou can access it here: ${document.getElementById('quickShareUrl').value}\n\nBest regards`);
    window.open(`mailto:?subject=${subject}&body=${body}`, '_blank');
}

// Event listeners for modal close
document.getElementById('cancelDelete').addEventListener('click', function() {
    document.getElementById('deleteModal').classList.add('hidden');
});

document.getElementById('closeShare').addEventListener('click', function() {
    document.getElementById('shareModal').classList.add('hidden');
});

// Close modals when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});

document.getElementById('shareModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
    }
});
</script>
@endsection
