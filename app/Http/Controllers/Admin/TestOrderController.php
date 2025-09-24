<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class TestOrderController extends Controller
{
    public function test()
    {
        return view('admin.test-orders');
    }
}
