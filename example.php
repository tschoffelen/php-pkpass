<?php
require('PKPass.php');

$pass = new PKPass();

$pass->setCertificate('/path/to/Certificate.p12'); // Set the path to your Pass Certificate (.p12 file)
$pass->setCertificatePassword('test123'); // Set password for certificate

$pass->setJSON('{ 
    "passTypeIdentifier": "pass.com.apple.test",
    "formatVersion": 1,
    "organizationName": "Flight Express",
    "serialNumber": "123456",
    "teamIdentifier": "AGK5BZEN3E",
    "backgroundColor": "rgb(107,156,196)",
    "logoText": "Flight info",
    "boardingPass": {
        "primaryFields": [
            {
            	"key" : "origin",
            	"label" : "San Francisco",
            	"value" : "SFO"
            },
            {
            	"key" : "destination",
            	"label" : "London",
            	"value" : "LHR"
            }
        ],
        "secondaryFields": [
            {
                "key": "gate",
                "label": "Gate",
                "value": "F12"
            },
            {
                "key": "date",
                "label": "Departure date",
                "value": "07/11/2012 10:22"
            }

        ],
        "backFields": [
            {
                "key": "passenger-name",
                "label": "Passenger",
                "value": "John Appleseed"
            }
        ],
        "transitType" : "PKTransitTypeAir"
    },
    "barcode": {
        "format": "PKBarcodeFormatQR",
        "message": "Flight-GateF12-ID6643679AH7B",
        "messageEncoding": "iso-8859-1"
    }
}');

// add files to the PKPass package
$pass->addFile('images/icon.png');
$pass->addFile('images/icon@2x.png');
$pass->addFile('images/logo.png');

$pass->create(); // Create and output the PKPass
?>