<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrdersuccessController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index']]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (session()->has('invoiceNumber')) {
            return view('ordersuccess');
        }

        return redirect()->route('summary');
    }
}
