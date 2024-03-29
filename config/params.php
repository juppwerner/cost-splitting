<?php

return [
    // Year this application was released
    'applicationReleaseYear' => 2023,
    // Bootswatch UI theme
    // cerulean, cosmo, cyborg, darkly, flatly, journal, litera, lumen, 
    // lux, materia, minty, pulse, sandstone, simplex, sketchy, 
    // slate, solar, spacelab, superhero, united, yeti
    // (see vendor/thomaspark/bootswatch directory names)
    'theme' => 'cerulean', 

    // Can be used to receive administrative emails (To:):
    'adminEmail'                    => 'admin@example.com',
    // *** Contact Form Settings ***
    // Send contact form email using this email as sender (From:):
    'contactForm.senderEmail'       => 'noreply@example.com',
    // Send contact form email using this name as sender (From:):
    'contactForm.senderName'        => 'Example.com mailer',
    // Send contact form email to this email (To:):
    'contactForm.recipientEmail'    => 'noreply@example.com',
    
    // Max. number of cost projects per user. 0=unlimited
    'user.maxNbrOfCostProjects' => 1,
    
    // set a default Bootstrap version globally for all Krajee Extensions
    'bsVersion'                     => '4.x',    

    // PayPal payment settings
    // Client ID:
    'paypal.clientId' => null,
    // Client Secret:
    'paypal.clientSecret' => null,

    // Payment options
    // Currency Code to be used (3 char. code):
    'paymentCurrencyCode' => null,
    // List of payment rates:
    'paymentOptions' => null,

];
