<?php
require('src/PKPass.php');

$pass = new PKPass\PKPass();

$pass->setCertificate('../Certificate.p12');  // 2. Set the path to your Pass Certificate (.p12 file)
$pass->setCertificatePassword('test123');     // 2. Set password for certificate
$pass->setWWDRcertPath('../AppleWWDRCA.pem'); // 3. Set the path to your WWDR Intermediate certificate (.pem file)

// Top-Level Keys http://developer.apple.com/library/ios/#documentation/userexperience/Reference/PassKit_Bundle/Chapters/TopLevel.html
$standardKeys         = array(
    'description'        => 'Demo pass',
    'formatVersion'      => 1,
    'organizationName'   => 'Flight Express',
    'passTypeIdentifier' => 'pass.com.apple.test', // 4. Set to yours
    'serialNumber'       => '123456',
    'teamIdentifier'     => 'AGK5BZEN3E'           // 4. Set to yours
);
$associatedAppKeys    = array();
$relevanceKeys        = array();
$styleKeys            = array(
    'boardingPass' => array(
        'primaryFields' => array(
            array(
                'key'   => 'origin',
                'label' => 'San Francisco',
                'value' => 'SFO'
            ),
            array(
                'key'   => 'destination',
                'label' => 'London',
                'value' => 'LHR'
            )
        ),
        'secondaryFields' => array(
            array(
                'key'   => 'gate',
                'label' => 'Gate',
                'value' => 'F12'
            ),
            array(
                'key'   => 'date',
                'label' => 'Departure date',
                'value' => '07/11/2012 10:22'
            )
        ),
        'backFields' => array(
            array(
                'key'   => 'passenger-name',
                'label' => 'Passenger',
                'value' => 'John Appleseed'
            )
        ),
        'transitType' => 'PKTransitTypeAir'
    )
);
$visualAppearanceKeys = array(
    'barcode'         => array(
        'format'          => 'PKBarcodeFormatQR',
        'message'         => 'Flight-GateF12-ID6643679AH7B',
        'messageEncoding' => 'iso-8859-1'
    ),
    'backgroundColor' => 'rgb(107,156,196)',
    'logoText'        => 'Flight info'
);
$webServiceKeys       = array();

// Merge all pass data and set JSON for $pass object
$passData = array_merge(
    $standardKeys,
    $associatedAppKeys,
    $relevanceKeys,
    $styleKeys,
    $visualAppearanceKeys,
    $webServiceKeys
);

$pass->setJSON(json_encode($passData));

// Add files to the PKPass package
$pass->addFile('images/icon.png');
$pass->addFile('images/icon@2x.png');
$pass->addFile('images/logo.png');

if(!$pass->create(true)) { // Create and output the PKPass
    echo 'Error: '.$pass->getError();
}