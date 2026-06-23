<?php

namespace App\Http\Controllers;

use App\Models\ReceiptSetting;

class WelcomeController extends Controller
{
    public function index()
    {
        $brand = ReceiptSetting::firstOrNew([]);
        return view('welcome', compact('brand'));
    }
}
