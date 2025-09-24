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
        $qrCodes = QrCode::with(['store', 'product', 'order'])
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
            'ean_code' => 'nullable|string|size:13|unique:qr_codes,ean_code',
        ]);

        // Generate reference code
        $refCode = 'qr_' . Str::random(8) . '_' . time();

        // Create QR code
        $qrCode = QrCode::create([
            'store_id' => $request->store_id,
            'name' => $request->name,
            'question' => $request->question,
            'ref_code' => $refCode,
            'ean_code' => $request->ean_code,
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
            'ean_code' => 'nullable|string|size:13|unique:qr_codes,ean_code,' . $qrCode->id,
        ]);

        $qrCode->update([
            'store_id' => $request->store_id,
            'name' => $request->name,
            'question' => $request->question,
            'ean_code' => $request->ean_code,
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

        // Genera link personalizzato se ean_code è presente, altrimenti fallback
        $qrContentString = $qrCode->ean_code
            ? rtrim(config('app.url'), '/') . '/qr/' . $qrCode->ean_code
            : $qrCode->getQrUrl();

        // Recupera logo store se esiste
        $storeLogoPath = $qrCode->store && $qrCode->store->logo ? storage_path('app/public/' . $qrCode->store->logo) : null;
        $hasLogo = $storeLogoPath && file_exists($storeLogoPath);

        try {
            $qr = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                ->size(300)
                ->margin(1)
                ->errorCorrection('H')
                ->color(0,0,0)
                ->backgroundColor(255,255,255);
            $qrContent = $qr->generate($qrContentString);
            $fileName = 'qr-codes/' . $qrCode->ref_code . '.svg';
            Storage::disk('public')->put($fileName, $qrContent);
            $qrCode->update(['qr_code_image' => $fileName]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('QR Code generation failed', [
                'qr_code_id' => $qrCode->id,
                'error' => $e->getMessage(),
                'url' => $qrContentString
            ]);
            // Fallback: Use an external QR code service (no logo)
            try {
                $externalQrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrContentString) . "&color=000000&bgcolor=FFFFFF";
                $qrImageContent = file_get_contents($externalQrUrl);
                if ($qrImageContent) {
                    $fileName = 'qr-codes/' . $qrCode->ref_code . '.png';
                    Storage::disk('public')->put($fileName, $qrImageContent);
                    $qrCode->update(['qr_code_image' => $fileName]);
                }
            } catch (\Exception $e2) {
                \Log::error('QR fallback generation failed', [
                    'qr_code_id' => $qrCode->id,
                    'error' => $e2->getMessage(),
                    'url' => $qrContentString
                ]);
            }
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

    /**
     * Show a printable label for the QR code (etichetta per vaso).
     */
    public function label(QrCode $qrCode)
    {
        $qrCode->load('store');
        return view('admin.qr-codes.label', compact('qrCode'));
    }
}
