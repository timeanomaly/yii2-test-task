<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'bsVersion' => '4.x',
    'jwt' => [
        'issuer' => 'https://api.example.com',  //name of your project (for information only)
        'audience' => 'https://frontend.example.com',  //description of the audience, eg. the website using the authentication (for info only)
        'id' => 'U6n2GV4zKzGDsJ1mNEViJ5w1eutsMtu2Heg3dIJg',  //a unique identifier for the JWT, typically a random string
        'expire' => 300,  //the short-lived JWT token is here set to expire after 5 min.
    ],
];
