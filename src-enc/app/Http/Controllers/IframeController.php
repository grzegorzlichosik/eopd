<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class IframeController extends Controller
{
    public function store(Request $request): Response
    {
        return response('Data Posted to iframe: ' . $request->get('number'));
    }
}
