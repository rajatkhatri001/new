<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EmailSubscription;

class EmailSubscriptionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        EmailSubscription::create([
            'email' => $request->email,
        ]);

        return response()->json(['success' => 'Thank you for subscribing!']);
    }
}
