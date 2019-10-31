<?php

namespace App\Http\Controllers;

use App\Package;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $packages = Package::orderBy('created_at', 'desc')
            ->get();
        return view('welcome')
            ->with('packages',$packages);
    }
}
