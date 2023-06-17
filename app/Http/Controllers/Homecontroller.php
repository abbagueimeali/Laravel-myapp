<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class Homecontroller extends Controller
{
    //La page/vue home.blade.php
    public function home()
    {
        return view('home.home');
    }

    //La page/vue about.blade.php
    public function about()
    {
        return view('home.about');
    }

    //La page/vue dashboard.blade.php
    public function dashboard()
    {
        return view('home.dashboard');
    }
}
