<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;


class ContactController extends Controller
{
    public function send(Request $request)
{
    $data = $request->all();

    // إرسال الإيميل
    Mail::send('emails.contact', ['data' => $data], function ($message) use ($data) {
        $message->to('sabaatia2020@gmail.com') // غيّريه لإيميلك
                ->subject('New Demo Request from ' . $data['fullName']);
    });

    return back()->with('success', 'Your message has been sent successfully!');
}
}
