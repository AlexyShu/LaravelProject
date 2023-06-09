<?php

namespace App\Http\Controllers;

use App\Models\Good;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home', [
            'goods' => Good::query()->orderBy('id', 'DESC')->paginate(6),
        ]);
    }
}
