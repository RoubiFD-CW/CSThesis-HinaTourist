<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PublicVisitorController extends Controller
{
    public function showForm()
    {
        return view('visitor.pass');
    }
}
