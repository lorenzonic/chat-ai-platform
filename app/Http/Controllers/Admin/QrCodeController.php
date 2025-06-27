<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\QrCode;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class QrCodeController extends Controller
{
    /**
     * Display a listing of QR codes.
     */
    public function index(): View
    {
        $qrCodes = QrCode::with('store')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.qr-codes.index', compact('qrCodes'));
    }

    /**
     * Show the form for creating a new QR code.
     */
    public function create(): View
    {
        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.qr-codes.create', compact('stores'));
    }

    /**
     * Store a newly created QR code.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'question' => 'nullable|string|max:500',
        ]);

        // Generate reference code
        $refCode = 'qr_' . Str::random(8) . '_' . time();

        // Create QR code
        $qrCode = QrCode::create([
            'store_id' => $request->store_id,
            'name' => $request->name,
            'question' => $request->question,
            'ref_code' => $refCode,
        ]);

        // Generate the QR code image
        $this->generateQrCodeImage($qrCode);

        return redirect()
            ->route('admin.qr-codes.show', $qrCode)
            ->with('success', 'QR Code created successfully!');
    }

    /**
     * Display the specified QR code.
     */
    public function show(QrCode $qrCode): View
    {
        $qrCode->load('store');

        // Generate QR image if it doesn't exist
        if (!$qrCode->qr_code_image || !Storage::disk('public')->exists($qrCode->qr_code_image)) {
            $this->generateQrCodeImage($qrCode);
        }

        // Generate dummy stats for now (you can implement real analytics later)
        $stats = $qrCode->stats;

        return view('admin.qr-codes.show', compact('qrCode', 'stats'));
    }

    /**
     * Show the form for editing the specified QR code.
     */
    public function edit(QrCode $qrCode): View
    {
        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.qr-codes.edit', compact('qrCode', 'stores'));
    }

    /**
     * Update the specified QR code.
     */
    public function update(Request $request, QrCode $qrCode): RedirectResponse
    {
        $request->validate([
            'store_id' => 'required|exists:stores,id',
            'name' => 'required|string|max:255',
            'question' => 'nullable|string|max:500',
        ]);

        $qrCode->update([
            'store_id' => $request->store_id,
            'name' => $request->name,
            'question' => $request->question,
        ]);

        // Regenerate QR code image when updated
        $this->generateQrCodeImage($qrCode);

        return redirect()
            ->route('admin.qr-codes.show', $qrCode)
            ->with('success', 'QR Code updated and regenerated successfully!');
    }

    /**
     * Remove the specified QR code.
     */
    public function destroy(QrCode $qrCode): RedirectResponse
    {
        // Delete the image file
        if ($qrCode->qr_code_image && Storage::disk('public')->exists($qrCode->qr_code_image)) {
            Storage::disk('public')->delete($qrCode->qr_code_image);
        }

        $qrCode->delete();

        return redirect()
            ->route('admin.qr-codes.index')
            ->with('success', 'QR Code deleted successfully!');
    }

    /**
     * Download QR code image.
     */
    public function download(QrCode $qrCode)
    {
        if (!$qrCode->qr_code_image || !Storage::disk('public')->exists($qrCode->qr_code_image)) {
            $this->generateQrCodeImage($qrCode);
        }

        // Determine file extension from the stored filename
        $extension = pathinfo($qrCode->qr_code_image, PATHINFO_EXTENSION);
        $filename = Str::slug($qrCode->name) . '-qr-code.' . $extension;

        return Storage::disk('public')->download($qrCode->qr_code_image, $filename);
    }    /**
     * Generate QR code image and save to storage
     */
    private function generateQrCodeImage(QrCode $qrCode): void
    {
        // Delete old image if exists
        if ($qrCode->qr_code_image && Storage::disk('public')->exists($qrCode->qr_code_image)) {
            Storage::disk('public')->delete($qrCode->qr_code_image);
        }

        // Generate QR code URL
        $qrUrl = $qrCode->getQrUrl();

        try {
            // Try the simple-qrcode package first
            $qrContent = QrCodeGenerator::size(300)
                ->margin(1)
                ->errorCorrection('M')
                ->generate($qrUrl);

            // Save the QR code content (should be SVG by default)
            $fileName = 'qr-codes/' . $qrCode->ref_code . '.svg';
            Storage::disk('public')->put($fileName, $qrContent);

            // Update QR code record
            $qrCode->update(['qr_code_image' => $fileName]);

        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('QR Code generation failed with simple-qrcode', [
                'qr_code_id' => $qrCode->id,
                'error' => $e->getMessage(),
                'url' => $qrUrl
            ]);

            // Fallback: Use an external QR code service
            try {
                $externalQrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrUrl);
                $qrImageContent = file_get_contents($externalQrUrl);

                if ($qrImageContent) {
                    $fileName = 'qr-codes/' . $qrCode->ref_code . '.png';
                    Storage::disk('public')->put($fileName, $qrImageContent);
                    $qrCode->update(['qr_code_image' => $fileName]);
                    return;
                }
            } catch (\Exception $externalError) {
                Log::error('External QR Code service failed', [
                    'qr_code_id' => $qrCode->id,
                    'error' => $externalError->getMessage()
                ]);
            }

            // Final fallback: Generate a simple placeholder SVG
            $placeholderSvg = '<?xml version="1.0" encoding="UTF-8"?>
<svg width="300" height="300" xmlns="http://www.w3.org/2000/svg">
    <rect width="300" height="300" fill="white" stroke="#ccc" stroke-width="2"/>
    <rect x="20" y="20" width="260" height="260" fill="#f8f9fa" stroke="#dee2e6" stroke-width="1"/>
    <circle cx="150" cy="120" r="30" fill="#6c757d"/>
    <text x="150" y="170" text-anchor="middle" font-family="Arial, sans-serif" font-size="16" fill="#495057">QR Code</text>
    <text x="150" y="190" text-anchor="middle" font-family="Arial, sans-serif" font-size="12" fill="#6c757d">' . htmlspecialchars($qrCode->name) . '</text>
    <text x="150" y="220" text-anchor="middle" font-family="Arial, sans-serif" font-size="10" fill="#adb5bd">Generazione fallita</text>
    <text x="150" y="235" text-anchor="middle" font-family="Arial, sans-serif" font-size="10" fill="#adb5bd">Prova a rigenerare</text>
</svg>';

            $fileName = 'qr-codes/' . $qrCode->ref_code . '.svg';
            Storage::disk('public')->put($fileName, $placeholderSvg);
            $qrCode->update(['qr_code_image' => $fileName]);
        }
    }

    /**
     * Regenerate QR code image
     */
    public function regenerate(QrCode $qrCode): RedirectResponse
    {
        try {
            $this->generateQrCodeImage($qrCode);

            return redirect()
                ->route('admin.qr-codes.show', $qrCode)
                ->with('success', 'QR Code regenerated successfully!');
        } catch (\Exception $e) {
            Log::error('QR Code regeneration failed', [
                'qr_code_id' => $qrCode->id,
                'error' => $e->getMessage()
            ]);

            return redirect()
                ->route('admin.qr-codes.show', $qrCode)
                ->with('error', 'Failed to regenerate QR Code. Please try again.');
        }
    }
}
