<?php

namespace App\Http\Controllers;

use App\Http\Traits\Ums\UserTrait;
use Illuminate\Http\Request;

class UserController extends Controller
{
    use UserTrait;
    public function registration_complete($id)
    {
        $params = [
            'icon' => 'fas fa-money-check',
            'page_title' => 'Escrow Admin',
        ];
        return view('ext')->with($params);
    }
}
