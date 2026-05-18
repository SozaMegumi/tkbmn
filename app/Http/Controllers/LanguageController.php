<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function swap($locale)
    {
        // Pastikan hanya bahasa yang dibenarkan sahaja diterima
        if (in_array($locale, ['en', 'ms'])) {
            Session::put('locale', $locale);
        }
        
        return redirect()->back(); // Patah balik ke page sebelum ini
    }
}