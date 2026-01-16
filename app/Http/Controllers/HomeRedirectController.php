<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeRedirectController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = $request->user();

        return match ($user->role) {
            'admin'     => redirect()->route('admin.events.index'),      
            'volunteer' => redirect()->route('volunteer.explore.index'),    
            default     => redirect()->route('landing'),              
        };
    }
}

