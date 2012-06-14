<?php
if(isset($_POST['name'])){
	// User has filled in the card info, so create the pass now
	
	setlocale(LC_MONETARY, 'en_US');
	require('../PKPass.php');
		
	// Variables
	$id = rand(100000,999999) . '-' . rand(100,999) . '-' . rand(100,999); // Every card should have a unique serialNumber
	$balance = '$'.rand(0,30).'.'.rand(10,99); // Create random balance
	$name = $_POST['name'];
	
	
	// Create pass
	$pass = new PKPass(); 

	$pass->setCertificate('../../Certificate.p12'); // Set the path to your Pass Certificate (.p12 file)
	$pass->setCertificatePassword('test123'); // Set password for certificate

	$pass->setJSON('{ 
	"passTypeIdentifier": "pass.com.apple.test",
	"formatVersion": 1,
	"organizationName": "Starbucks",
	"teamIdentifier": "AGK5BZEN3E",
	"serialNumber": "'.$id.'",
    "backgroundColor": "rgb(240,240,240)",
	"logoText": "Starbucks",
	"storeCard": {
        "secondaryFields": [
            {
                "key": "balance",
                "label": "BALANCE",
                "value": "'.$balance.'"
            },
            {
                "key": "name",
                "label": "NICKNAME",
                "value": "'.$name.'"
            }

        ],
        "backFields": [
            {
                "key": "id",
                "label": "Card Number",
                "value": "'.$id.'"
            }
        ]
    },
    "barcode": {
        "format": "PKBarcodeFormatPDF417",
        "message": "'.$id.'",
        "messageEncoding": "iso-8859-1",
        "altText": "'.$id.'"
    }
    }');

    // add files to the PKPass package
    $pass->addFile('icon.png');
    $pass->addFile('icon@2x.png');
    $pass->addFile('logo.png');
    $pass->addFile('background.png');

    $pass->create(); // Create and output the PKPass
    exit;
	
}else{
	// User lands here, there are no $_POST variables set	
	?>
	<html>
		<head>
			<title>Flight pass creator - PHP class demo</title>
			
			<!-- Reusing some CSS from another project of mine -->
			<link href="http://www.lifeschool.nl/static/css" rel="stylesheet" type="text/css" />
			<meta name="viewport" content="width=320; user-scalable=no" />
			<style>
				.header { background-color: #CCC; padding-top: 30px; padding-bottom: 30px; margin-bottom: 32px; text-align: center; }
				.logo { width: 84px; height: 84px; margin-bottom: 20px; }
				.title { color: black; font-size: 22px; text-shadow: 1px 1px 1px rgba(0,0,0,0.1); font-weight: bold; display: block; text-align: center; }
				.userinfo { margin: 0px auto; padding-bottom: 32px; width: 280px;}
				form.form-stacked { padding: 0px;}
				legend { text-align: center; padding-bottom: 20px; clear: both;}
				input.xlarge { width: 280px; height: 26px; line-height: 26px;}
			</style>
		</head>
		<body>
			<div class="header">
				<img class="logo" src="logo_web.png" />
				<span class="title">Starbucks</span>
			</div>
			<div class="userinfo">
				<form action="index.php" method="post" class="form-stacked">
            <fieldset>
                <legend style="padding-left: 0px;">Please enter your info</legend>
                                                
                <div class="clearfix">
                	<label style="text-align:left">Nickname</label>
                	<div class="input">
                		<input class="xlarge" name="name" type="text" value="Johnny's card" />
                	</div>
                </div>
                                
                <br /><br />
                <center><input type="submit" class="btn primary" value=" Create pass &gt; " /></center>
            </fieldset>
        </form>

			</div>
		</body>
	</html>
	<?
} 
?>