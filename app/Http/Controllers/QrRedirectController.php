<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class QrRedirectController extends Controller
{
    /**
     * Redirect from /qr/{ean_code} to the store chatbot or show 404 if not found.
     */
    public function redirect($ean_code)
    {
        $qrCode = QrCode::where('ean_code', $ean_code)->first();
        if (!$qrCode) {
            abort(404, 'QR code not found');
        }
        // Redirect to the store chatbot page with ref code if available
        $url = route('store.chatbot', $qrCode->store->slug);
        $params = [];
        if ($qrCode->ref_code) {
            $params['ref'] = $qrCode->ref_code;
        }
        if ($qrCode->question) {
            $params['question'] = $qrCode->question;
        }
        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }
        return Redirect::to($url);
    }
}
