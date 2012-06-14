<?php
class PKPass{
	var $certPath;
	var $files = array();
	var $JSON;
	var $SHAs;
	var $certPass = '';
	var $tempPath = '/tmp/'; // Must end with slash!

	function setCertificate($path){
		if(file_exists($path)){
			$this->certPath = $path;
		}else{
			die('Error: certificate path is incorrect (file does not exist).');
		}
	}
	function setCertificatePassword($p){
		$this->certPass = $p;
	}
	function setJSON($JSON){
		if(@json_decode($JSON) != false){
			$this->JSON = $JSON;
		}else{
			die('Error: couldn\'t parse JSON string.');
		}
	}
	function addFile($path){
		if(file_exists($path)){
			$this->files[] = $path;
		}else{
			die('Error: file "'.$path.'" does not exist.');
		}
	}
	function create(){
		// Create SHA hashes for all files in package
		$this->SHAs['pass.json'] = sha1($this->JSON);
		foreach($this->files as $file){
			$this->SHAs[basename($file)] = sha1(file_get_contents($file));
		}
		$manifest = json_encode((object)$this->SHAs);
		file_put_contents($this->tempPath.'manifest.json',$manifest);
		
		// Create signature
		exec('openssl pkcs12 -in "'.$this->certPath.'" -clcerts -nokeys -out '.$this->tempPath.'certificate.pem -passin pass:"'.$this->certPass.'"');
		exec('openssl pkcs12 -in "'.$this->certPath.'" -nocerts -out '.$this->tempPath.'key.pem -passin pass:"'.$this->certPass.'" -passout pass:"'.$this->certPass.'"');
		exec('openssl smime -binary -sign -signer '.$this->tempPath.'certificate.pem -inkey '.$this->tempPath.'key.pem -in '.$this->tempPath.'manifest.json -out '.$this->tempPath.'signature -outform DER -passin pass:"'.$this->certPass.'"');
		unlink($this->tempPath.'certificate.pem');
		unlink($this->tempPath.'key.pem');
		unlink($this->tempPath.'manifest.json');
		
		// Package file in Zip (as .pkpass)
		$zip = new ZipArchive();
		if(!$zip->open($this->tempPath."pass.pkpass", ZIPARCHIVE::CREATE)){
			die('Error: could not open pass.pkpass with ZipArchive extension.');
		}
		$zip->addFile($this->tempPath.'signature','signature');
		$zip->addFromString('manifest.json',$manifest);
		$zip->addFromString('pass.json',$this->JSON);
		foreach($this->files as $file){
			$zip->addFile($file,basename($file));
		}
		$zip->close();
		
		// Check if pass is created and valid
		if(!file_exists($this->tempPath.'pass.pkpass') || filesize($this->tempPath.'pass.pkpass') < 1){
			die('Error: error while creating pass.pkpass. Check your Zip extension.');
		}
		
		// Output pass
		header('Pragma: no-cache');
		header('Content-type: application/vnd.apple.pkpass');
		header('Content-length: '.filesize($this->tempPath."pass.pkpass"));
		header('Content-Disposition: attachment; filename="pass.pkpass"');
		echo file_get_contents($this->tempPath.'pass.pkpass');
		
		// Cleanup
		unlink($this->tempPath.'pass.pkpass');
		unlink($this->tempPath.'signature');
	}
}
?>