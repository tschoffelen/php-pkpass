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

if(isset($_POST['passenger'])) {
    // User has filled in the flight info, so create the pass now

    // Predefined data
    $labels = [
        'SFO' => 'San Francisco',
        'LAX' => 'Los Angeles',
        'LHR' => 'London',
    ];
    $gates = ['F12', 'G43', 'A2', 'C5', 'K9'];

    // User-set vars
    $passenger = addslashes($_POST['passenger']);
    $origin = $_POST['origin'];
    $origin_label = $labels[$origin];
    $destination = $_POST['destination'];
    $destination_label = $labels[$destination];
    $gate = $gates[array_rand($gates)]; // Yup, pick a random gate
    $date = date('m/d/Y H:i', $_POST['date']); // Convert date to string

    // Create pass

    //Set certificate and path in the constructor
    $pass = new PKPass('../../Certificates.p12', 'password');

    //Check if an error occurred within the constructor
    if($pass->checkError($error) == true) {
        exit('An error occurred: ' . $error);
    }

    // Set pass data
    $pass->setData('{
	"passTypeIdentifier": "pass.com.scholica.flights",
	"formatVersion": 1,
	"organizationName": "Flight Express",
	"serialNumber": "123456",
	"teamIdentifier": "KN44X8ZLNC",
	"backgroundColor": "rgb(32,110,247)",
	"logoText": "FLIGHT_INFO_LABEL",
	"description": "Demo pass",
	"boardingPass": {
        "primaryFields": [
            {
            	"key" : "origin",
            	"label" : "' . $origin_label . '",
            	"value" : "' . $origin . '"
            },
            {
            	"key" : "destination",
            	"label" : "' . $destination_label . '",
            	"value" : "' . $destination . '"
            }
        ],
        "secondaryFields": [
            {
                "key": "gate",
                "label": "GATE_LABEL",
                "value": "' . $gate . '"
            },
            {
                "key": "date",
                "label": "DEPARTURE_DATE_LABEL",
                "value": "' . $date . '"
            }

        ],
        "backFields": [
            {
                "key": "passenger-name",
                "label": "Passenger",
                "value": "' . $passenger . '"
            }
        ],
        "transitType" : "PKTransitTypeAir"
    },
    "barcode": {
        "format": "PKBarcodeFormatQR",
        "message": "Flight-Gate' . $gate . '-' . $date . '-' . $passenger . '-' . $destination . '",
        "messageEncoding": "iso-8859-1"
    },
    "relevantDate": "' . date('Y-m-d\TH:i:sP', $_POST['date']) . '"
    }');
    if($pass->checkError($error) == true) {
        exit('An error occured: ' . $error);
    }

    // Add files to the PKPass package
    $pass->addFile('../images/icon.png');
    $pass->addFile('../images/icon@2x.png');
    $pass->addFile('../images/logo.png');
    // Specify english and french localizations
    $pass->addFile('en.strings', 'en.lproj/pass.strings');
    $pass->addFile('fr.strings', 'fr.lproj/pass.strings');

    if($pass->checkError($error) == true) {
        exit('An error occured: ' . $error);
    }
    // Create and output the PKPass
    // If you pass true, the class will output the zip into the browser.
    $result = $pass->create(true);
    if($result == false) {
        echo $pass->getError();
    }
} else {
    // User lands here, there are no $_POST variables set
	?>
	<html>
	<head>
		<title>Flight pass creator - PHP class demo</title>
		<meta name="viewport" content="width=320; user-scalable=no"/>
		<style>
            body {
                font-family: Helvetica, sans-serif;
            }
			.header {
				color: white;
				background-color: #6699cc;
				padding-top: 30px;
				padding-bottom: 30px;
				margin-bottom: 32px;
				text-align: center;
			}

			.logo {
				width: 64px;
				height: 64px;
				margin-bottom: 20px;
			}

			.title {
				color: white;
				font-size: 22px;
				text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.5);
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
				clear: both;
				border-bottom: none;
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
		<img class="logo" src="icon.png"/> <span class="title">Air Company</span>
	</div>
	<div class="userinfo">
		<form action="index.php" method="post" class="form-stacked">
			<fieldset>
				<legend style="padding-left: 0;">Please enter your info</legend>

				<div class="clearfix">
					<label style="text-align:left">Flight schedule</label>
					<div class="input">
						<select name="origin" style="width: auto;">
							<option value="SFO">San Francisco</option>
							<option value="LAX">Los Angeles</option>
							<option value="LHR">London</option>
						</select> &nbsp; to &nbsp; <select name="destination" style="width: auto;">
							<option value="SFO">San Francisco</option>
							<option value="LAX">Los Angeles</option>
							<option value="LHR">London</option>
						</select>
					</div>
				</div>

				<div class="clearfix">
					<label style="text-align:left">Passenger name</label>
					<div class="input">
						<input class="xlarge" name="passenger" type="text" value="" placeholder="John Appleseed"/>
					</div>
				</div>

				<div class="clearfix">
					<label style="text-align:left">Flight date</label>
					<div class="input">
						<select name="date" style="width: 100%;">
							<option value="<?= time(); ?>">Today</option>
							<option value="<?= (time() + 86400); ?>">Tomorrow</option>
							<option value="<?= (time() + (86400 * 7)); ?>">Next week</option>
						</select>
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