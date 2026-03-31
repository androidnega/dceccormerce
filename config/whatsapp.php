<?php

return [

    'enabled' => env('WHATSAPP_ENABLED', false),

    'account_sid' => env('TWILIO_ACCOUNT_SID', ''),

    'auth_token' => env('TWILIO_AUTH_TOKEN', ''),

    /*
    | Twilio WhatsApp-enabled sender, e.g. whatsapp:+14155238886
    */
    'from' => env('TWILIO_WHATSAPP_FROM', ''),

];
