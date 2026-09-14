<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(Request $request): View
    {
        return view('pages.contact', [
            'branch' => Branch::first(),
        ]);
    }
}
