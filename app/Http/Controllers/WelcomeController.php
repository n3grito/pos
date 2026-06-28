<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\ReceiptSetting;

class WelcomeController extends Controller
{
    public function index()
    {
        $brand = ReceiptSetting::firstOrNew([]);
        $welcomeContent = GeneralSetting::get('welcome_content', '');

        return view('welcome', compact('brand', 'welcomeContent'));
    }
}
