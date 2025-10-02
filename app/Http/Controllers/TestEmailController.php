<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

class TestEmailController extends Controller
{
    public function sendTestEmail()
    {
        try {
            // $details = [
            //     'title' => 'Test Email from Laravel',
            //     'body' => 'This is a test email to verify that your SMTP settings are working correctly.'
            // ];

            $details = [
                'name' => 'Taha',
                'code' => '123456',
                'expiry' => '60 minutes'
            ];

            Mail::to('tahashaban743@gmail.com')->send(new \App\Mail\ForgetPassword($details));
            
            return response()->json([
                'message' => 'Test email sent successfully!',
                'config' => [
                    'mailer' => config('mail.mailer'),
                    'from' => config('mail.from'),
                    'smtp' => [
                        'host' => config('mail.mailers.smtp.host'),
                        'port' => config('mail.mailers.smtp.port'),
                        'encryption' => config('mail.mailers.smtp.encryption'),
                        'username' => config('mail.mailers.smtp.username'),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error sending email',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
