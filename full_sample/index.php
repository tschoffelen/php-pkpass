<?php
if(isset($_POST['passenger'])){
	// User has filled in the flight info, so create the pass now
	
	require('../PKPass.php');
	
	// Predefined data
	$labels = array(
		'SFO' => 'San Francisco',
		'LAX' => 'Los Angeles',
		'LHR' => 'London'
	);
	$gates = array('F12','G43','A2','C5','K9');
	
	// User-set vars
	$passenger = addslashes($_POST['passenger']);
	$origin = $_POST['origin'];
	$origin_label = $labels[$origin];
	$destination = $_POST['destination'];
	$destination_label = $labels[$destination];
	$gate = $gates[array_rand($gates)]; // Yup, pick a random gate
	$date = date('m/d/Y H:i',$_POST['date']); // Convert date to string
	
	// Create pass
	
	$pass = new PKPass(); 

	$pass->setCertificate('../../Certificate.p12'); // Set the path to your Pass Certificate (.p12 file)
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
            	"label" : "'.$origin_label.'",
            	"value" : "'.$origin.'"
            },
            {
            	"key" : "destination",
            	"label" : "'.$destination_label.'",
            	"value" : "'.$destination.'"
            }
        ],
        "secondaryFields": [
            {
                "key": "gate",
                "label": "Gate",
                "value": "'.$gate.'"
            },
            {
                "key": "date",
                "label": "Departure date",
                "value": "'.$date.'"
            }

        ],
        "backFields": [
            {
                "key": "passenger-name",
                "label": "Passenger",
                "value": "'.$passenger.'"
            }
        ],
        "transitType" : "PKTransitTypeAir"
    },
    "barcode": {
        "format": "PKBarcodeFormatQR",
        "message": "Flight-Gate'.$gate.'-'.$date.'-'.$passenger.'-'.$destination.'",
        "messageEncoding": "iso-8859-1"
    }
    }');

    // add files to the PKPass package
    $pass->addFile('../images/icon.png');
    $pass->addFile('../images/icon@2x.png');
    $pass->addFile('../images/logo.png');

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
				.header { color: white; background-color: #6699cc; padding-top: 30px; padding-bottom: 30px; margin-bottom: 32px; text-align: center; }
				.logo { width: 64px; height: 64px; margin-bottom: 20px; }
				.title { color: white; font-size: 22px; text-shadow: 1px 1px 1px rgba(0,0,0,0.5); font-weight: bold; display: block; text-align: center; }
				.userinfo { margin: 0px auto; padding-bottom: 32px; width: 280px;}
				form.form-stacked { padding: 0px;}
				legend { text-align: center; padding-bottom: 20px; clear: both;}
				input.xlarge { width: 280px; height: 26px; line-height: 26px;}
			</style>
		</head>
		<body>
			<div class="header">
				<img class="logo" src="icon.png" />
				<span class="title">Air Company</span>
			</div>
			<div class="userinfo">
				<form action="index.php" method="post" class="form-stacked">
            <fieldset>
                <legend style="padding-left: 0px;">Please enter your info</legend>
                                
                <div class="clearfix">
                    <label style="text-align:left">Flight schedule</label>
                        <div class="input">
                            <select name="origin" style="width: auto;">
                                <option value="SFO" >San Francisco</option>
                                <option value="LAX" >Los Angeles</option>
                                <option value="LHR" >Londen</option>
                            </select> 
                            &nbsp; to &nbsp;
                            <select name="destination" style="width: auto;">
                                <option value="SFO" >San Francisco</option>
                                <option value="LAX" >Los Angeles</option>
                                <option value="LHR" >Londen</option>
                            </select>
                        </div>
                </div>
                
                <div class="clearfix">
                	<label style="text-align:left">Passenger name</label>
                	<div class="input">
                		<input class="xlarge" name="passenger" type="text" value="" placeholder="John Appleseed" />
                	</div>
                </div>
                
                <div class="clearfix">
                	<label style="text-align:left">Flight date</label>
                	<div class="input">
                		<select name="date" style="width: 100%;">
                                <option value="<?=time();?>">Today</option>
                                <option value="<?=(time()+86400);?>">Tomorrow</option>
                                <option value="<?=(time()+(86400*7));?>">Next week</option>
                            </select>
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