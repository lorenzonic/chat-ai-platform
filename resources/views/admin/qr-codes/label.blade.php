@extends('layouts.admin')

@section('title', 'Stampa Etichetta QR')

@section('content')
<style>
@media print {
    body { background: white !important; }
    .label-container { box-shadow: none !important; border: none !important; }
    .no-print { display: none !important; }
}
.label-container {
    width: 350px;
    height: 140px;
    display: flex;
    flex-direction: row;
    align-items: center;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
    box-shadow: 0 2px 8px rgba(0,0,0,0.07);
    margin: 40px auto;
    padding: 0 18px;
}
.label-qr {
    flex: 0 0 110px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.label-info {
    flex: 1;
    padding-left: 18px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    height: 100%;
}
.label-title {
    font-size: 1.25rem;
    font-weight: bold;
    color: #222;
    margin-bottom: 0.5rem;
    word-break: break-word;
}
.label-ean {
    font-size: 1rem;
    color: #555;
    font-family: monospace;
    margin-bottom: 0.5rem;
}
.label-store {
    font-size: 0.9rem;
    color: #888;
}
</style>
<div class="no-print" style="text-align:center; margin-top:30px;">
    <a href="{{ route('admin.qr-codes.show', $qrCode) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50">&larr; Torna al QR</a>
    <button onclick="printLabelOnly()" class="ml-4 px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">Stampa Etichetta</button>
</div>
<div id="label-print-area" class="label-container">
    <div class="label-qr">
        @if($qrCode->qr_code_image && Storage::disk('public')->exists($qrCode->qr_code_image))
            @if(pathinfo($qrCode->qr_code_image, PATHINFO_EXTENSION) === 'svg')
                <div style="width:100px; height:100px; display:flex; align-items:center; justify-content:center;">
                    {!! Storage::disk('public')->get($qrCode->qr_code_image) !!}
                </div>
            @else
                <img src="{{ Storage::disk('public')->url($qrCode->qr_code_image) }}" alt="QR Code" style="width:100px; height:100px;">
            @endif
        @else
            <div style="width:100px; height:100px; background:#eee; display:flex; align-items:center; justify-content:center; color:#aaa;">QR</div>
        @endif
    </div>
    <div class="label-info">
        <div class="label-title">{{ $qrCode->name }}</div>
        @if($qrCode->ean_code)
            <div class="label-ean">EAN: {{ $qrCode->ean_code }}</div>
        @endif
        <div class="label-store">{{ $qrCode->store->name }}</div>
    </div>
</div>
<script>
function printLabelOnly() {
    const printContents = document.getElementById('label-print-area').outerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    window.location.reload();
}
</script>
@endsection
