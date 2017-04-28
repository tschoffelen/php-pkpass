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

require('../../vendor/autoload.php');

if(isset($_POST['name'])) {
    // User has filled in the card info, so create the pass now

    setlocale(LC_MONETARY, 'en_US');

    // Variables
    $id = rand(100000, 999999) . '-' . rand(100, 999) . '-' . rand(100, 999); // Every card should have a unique serialNumber
    $balance = '$' . rand(0, 30) . '.' . rand(10, 99); // Create random balance
    $name = stripslashes($_POST['name']);

    // Create pass
    // Set the path to your Pass Certificate (.p12 file)
    $pass = new PKPass('../../Certificates.p12', 'password');
    $pass->setData('{
	"passTypeIdentifier": "pass.com.scholica.flights", 
	"formatVersion": 1,
	"organizationName": "Starbucks",
	"teamIdentifier": "KN44X8ZLNC",
	"serialNumber": "' . $id . '",
    "backgroundColor": "rgb(240,240,240)",
	"logoText": "Starbucks",
	"description": "Demo pass",
	"storeCard": {
        "secondaryFields": [
            {
                "key": "balance",
                "label": "BALANCE",
                "value": "' . $balance . '"
            },
            {
                "key": "name",
                "label": "NICKNAME",
                "value": "' . $name . '"
            }

        ],
        "backFields": [
            {
                "key": "id",
                "label": "Card Number",
                "value": "' . $id . '"
            }
        ]
    },
    "barcode": {
        "format": "PKBarcodeFormatPDF417",
        "message": "' . $id . '",
        "messageEncoding": "iso-8859-1",
        "altText": "' . $id . '"
    }
    }');

    // add files to the PKPass package
    $pass->addFile('icon.png');
    $pass->addFile('icon@2x.png');
    $pass->addFile('logo.png');
    $pass->addFile('background.png', 'strip.png');

    if(!$pass->create(true)) { // Create and output the PKPass
        echo 'Error: ' . $pass->getError();
    }
    exit;
} else {
    // User lands here, there are no $_POST variables set

    ?>
    <html>
    <head>
        <title>Starbucks pass creator - PHP class demo</title>
        <meta name="viewport" content="width=device-width; user-scalable=no"/>
        <style>
            body {
                font-family: Helvetica, sans-serif;
            }

            .header {
                background-color: #CCC;
                padding-top: 30px;
                padding-bottom: 30px;
                margin-bottom: 32px;
                text-align: center;
            }

            .logo {
                width: 84px;
                height: 84px;
                margin-bottom: 20px;
            }

            .title {
                color: black;
                font-size: 22px;
                text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
                font-weight: bold;
                display: block;
                text-align: center;
            }

            .userinfo {
                margin: 0 auto;
                padding-bottom: 32px;
                width: 280px;
            }

            form.form-stacked {
                padding: 0;
            }

            legend {
                text-align: center;
                padding-bottom: 25px;
                border-bottom: none;
                clear: both;
            }

            input.xlarge {
                width: 280px;
                height: 26px;
                line-height: 26px;
            }
        </style>
    </head>
    <body>
    <div class="header">
        <img class="logo" src="logo_web.png"/>
        <span class="title">Starbucks</span>
    </div>
    <div class="userinfo">
        <form action="index.php" method="post" class="form-stacked">
            <fieldset>
                <legend style="padding-left: 0;">Please enter your info</legend>

                <div class="clearfix">
                    <label style="text-align:left" for="name">Nickname</label>
                    <div class="input">
                        <input class="xlarge"
                               name="name"
                               id="name"
                               type="text"
                               value="Johnny's card"/>
                    </div>
                </div>

                <br/><br/>
                <center><input type="submit" class="btn primary" value=" Create pass &gt; "/></center>
            </fieldset>
        </form>

    </div>
    </body>
    </html>
<?php } ?>
