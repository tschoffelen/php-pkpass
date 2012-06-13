<?php
require('PKPass.php');

$pass = new PKPass();

$pass->setCertificate('Certificate.p12'); // Set the path to your certificate (.p12 file)
$pass->setCertificatePassword('test123');
$pass->setJSON('{
    "passTypeIdentifier": "pass.nl.mijnbc.test",
    "formatVersion": 1,
    "organizationName": "Bernardinuscollege",
    "serialNumber": "123456",
    "teamIdentifier": "AGKMBZTN3K",
    "backgroundColor": "rgb(0, 167, 229)",
    "logoText": "Bernardinuscollege",
    "storeCard": {
        "primaryFields": [
            {
                "key": "class",
                "label": "Gymnasium",
                "value": "G32"
            }
        ],
        "secondaryFields": [
            {
                "key": "board-gate",
                "changeMessage": "Gate changed to %@.",
                "label": "Naam",
                "value": "Tom Schoffelen"
            },
            {
                "key": "id",
                "changeMessage": "Gate changed to %@.",
                "label": "Leerlingnummer",
                "value": "405174"
            }

        ],
        "auxilaryFields": [
            {
                "key": "seat",
                "label": "Seat",
                "value": "7A"
            },
            {
                "key": "passenger-name",
                "label": "Passenger",
                "value": "John Appleseed"
            }
        ]
    },
    "barcode": {
        "format": "PKBarcodeFormatQR",
        "message": "Hello world!",
        "messageEncoding": "iso-8859-1"
    }
}');

$pass->addFile('static/background.png');
$pass->addFile('static/icon.png');
$pass->addFile('static/logo.png');
$pass->addFile('static/logo@2x.png');
$pass->addFile('static/icon@2x.png');
//$pass->addFile('static/logo@2x.png');

$pass->create();
//$pass->create('test/pass.pkpass');

?>