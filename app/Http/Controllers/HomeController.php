<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index()
    {
        $params = [
            'page_title' => 'Dashboard',
        ];
        return view('app')->with($params);
    }
}
