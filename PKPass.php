<?php
class PKPass{
	var $certPath;
	var $files = array();
	var $JSON;
	var $SHAs;
	var $certPass = '';

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
			if(stristr(basename($file),'icon') || stristr(basename($file),'logo')){
				$this->SHAs[ucfirst(basename($file))] = sha1(file_get_contents($file));
			}
			$this->SHAs[basename($file)] = sha1(file_get_contents($file));
		}
		$manifest = json_encode((object)$this->SHAs);
		
		// Create signature
		if(!openssl_pkcs12_read(file_get_contents($this->certPath), $p12cert, $this->certPass)){
			die('Error: could not read .p12 certificate.');
		}
		$pKey = openssl_get_privatekey($p12cert['pkey']);
		if(!openssl_sign($manifest, $signature, $pKey)){
			die('Error: could not create the pass signature. Please check your certificate.');
		}
		openssl_free_key($pKey);
		
		// Package file in Zip (as .pkpass)
		$zip = new ZipArchive();
		if(!$zip->open("/tmp/pass.pkpass", ZIPARCHIVE::CREATE)){
			die('Error: could not open pass.pkpass with ZipArchive extension.');
		}
		$zip->addFromString('signature',$signature);
		$zip->addFromString('manifest.json',$manifest);
		$zip->addFromString('pass.json',$this->JSON);
		foreach($this->files as $file){
			if(stristr(basename($file),'icon') || stristr(basename($file),'logo')){
				$zip->addFile($file,ucfirst(basename($file)));
			}
			$zip->addFile($file,basename($file));
		}
		$zip->close();
		
		// Check if pass is created and valid
		if(!file_exists('/tmp/pass.pkpass') || filesize('/tmp/pass.pkpass') < 1){
			die('Error: error while creating pass.pkpass. Check your Zip extension.');
		}
		
		// Output pass
		header('Pragma: no-cache');
		header('Content-type: application/vnd.apple.pkpass');
		header('Content-length: '.filesize("/tmp/pass.pkpass"));
		header('Content-Disposition: attachment; filename="pass.pkpass"');
		echo file_get_contents('/tmp/pass.pkpass');
		
		// Cleanup
		unlink('/tmp/pass.pkpass');
	}
}
?>