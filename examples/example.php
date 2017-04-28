<?php

/**
 * Copyright (c) 2017, Thomas Schoffelen BV.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to
 * deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
 * sell copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 */

use PKPass\PKPass;

require('../vendor/autoload.php');

// Replace the parameters below with the path to your .p12 certificate and the certificate password!
$pass = new PKPass('../Certificates.p12', 'password');

// Pass content
$data = [
    'description' => 'Demo pass',
    'formatVersion' => 1,
    'organizationName' => 'Flight Express',
    'passTypeIdentifier' => 'pass.com.scholica.flights', // Change this!
    'serialNumber' => '12345678',
    'teamIdentifier' => 'KN44X8ZLNC', // Change this!
    'boardingPass' => [
        'primaryFields' => [
            [
                'key' => 'origin',
                'label' => 'San Francisco',
                'value' => 'SFO',
            ],
            [
                'key' => 'destination',
                'label' => 'London',
                'value' => 'LHR',
            ],
        ],
        'secondaryFields' => [
            [
                'key' => 'gate',
                'label' => 'Gate',
                'value' => 'F12',
            ],
            [
                'key' => 'date',
                'label' => 'Departure date',
                'value' => '07/11/2012 10:22',
            ],
        ],
        'backFields' => [
            [
                'key' => 'passenger-name',
                'label' => 'Passenger',
                'value' => 'John Appleseed',
            ],
        ],
        'transitType' => 'PKTransitTypeAir',
    ],
    'barcode' => [
        'format' => 'PKBarcodeFormatQR',
        'message' => 'Flight-GateF12-ID6643679AH7B',
        'messageEncoding' => 'iso-8859-1',
    ],
    'backgroundColor' => 'rgb(32,110,247)',
    'logoText' => 'Flight info',
    'relevantDate' => date('Y-m-d\TH:i:sP')
];
$pass->setData($data);

// Add files to the pass package
$pass->addFile('images/icon.png');
$pass->addFile('images/icon@2x.png');
$pass->addFile('images/logo.png');

// Create and output the pass
if(!$pass->create(true)) {
    echo 'Error: ' . $pass->getError();
}
