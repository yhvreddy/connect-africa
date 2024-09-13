<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CASubscriptionController extends Controller
{

    public function __construct() {}

    public function getSubscriptionsList(Request $request)
    {
        return view('search.index');
    }
}
