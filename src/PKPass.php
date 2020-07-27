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

namespace PKPass;

use ZipArchive;

/**
 * Class PKPass.
 */
class PKPass
{
    /**
     * Holds the path to the certificate
     * Variable: string.
     */
    protected $certPath;

    /**
     * Name of the downloaded file.
     */
    protected $name;

    /**
     * Holds the files to include in the .pkpass
     * Variable: array.
     */
    protected $files = [];

    /**
     * Holds the remote file urls to include in the .pkpass
     * Variable: array.
     */
    protected $remote_file_urls = [];

    /**
     * Holds the json
     * Variable: class.
     */
    protected $json;

    /**
     * Holds the SHAs of the $files array
     * Variable: array.
     */
    protected $shas;

    /**
     * Holds the password to the certificate
     * Variable: string.
     */
    protected $certPass = '';

    /**
     * Holds the path to the WWDR Intermediate certificate
     * Variable: string.
     */
    protected $wwdrCertPath = '';

    /**
     * Holds the path to a temporary folder with trailing slash.
     */
    protected $tempPath;

    /**
     * Holds error info if an error occurred.
     */
    private $sError = '';

    /**
     * Holds a auto-generated uniqid to prevent overwriting other processes pass
     * files.
     */
    private $uniqid = null;

    /**
     * Holds array of localization details
     * Variable: array.
     */
    protected $locales = [];

    /**
     * PKPass constructor.
     *
     * @param string|bool $certPath
     * @param string|bool $certPass
     * @param string|bool $JSON
     */
    public function __construct($certPath = false, $certPass = false, $JSON = false)
    {
        $this->tempPath = sys_get_temp_dir() . '/';  // Must end with slash!
        $this->wwdrCertPath = __DIR__ . '/Certificate/AppleWWDRCA.pem';

        if($certPath != false) {
            $this->setCertificate($certPath);
        }
        if($certPass != false) {
            $this->setCertificatePassword($certPass);
        }
        if($JSON != false) {
            $this->setData($JSON);
        }
    }

    /**
     * Sets the path to a certificate
     * Parameter: string, path to certificate
     * Return: boolean, always true.
     *
     * @param $path
     *
     * @return bool
     */
    public function setCertificate($path)
    {
        $this->certPath = $path;

        return true;
    }

    /**
     * Sets the certificate's password
     * Parameter: string, password to the certificate
     * Return: boolean, always true.
     *
     * @param $p
     *
     * @return bool
     */
    public function setCertificatePassword($p)
    {
        $this->certPass = $p;

        return true;
    }

    /**
     * Sets the path to the WWDR Intermediate certificate
     * Parameter: string, path to certificate
     * Return: boolean, always true.
     *
     * @param $path
     *
     * @return bool
     */
    public function setWWDRcertPath($path)
    {
        $this->wwdrCertPath = $path;

        return true;
    }

    /**
     * Set the path to the temporary directory.
     *
     * @param string $path Path to temporary directory
     * @return bool
     */
    public function setTempPath($path)
    {
        if(is_dir($path)) {
            $this->tempPath = rtrim($path, '/') . '/';

            return true;
        }

        return false;
    }

    /**
     * Set JSON for pass.
     *
     * @deprecated in favor of `setData()`
     *
     * @param string|array $json
     * @return bool
     */
    public function setJSON($json)
    {
        return $this->setData($json);
    }

    /**
     * Set pass data.
     *
     * @param string|array $data
     * @return bool
     */
    public function setData($data)
    {
        // Array is passed as input
        if(is_array($data)) {
            $this->json = json_encode($data);

            return true;
        }

        // JSON string is passed as input
        if(json_decode($data) !== false) {
            $this->json = $data;

            return true;
        }

        $this->sError = 'This is not a JSON string.';

        return false;
    }

    /**
     * Add dictionary of strings for transilation.
     *
     * @param string $language language project need to be added
     * @param array $strings key value pair of transilation strings
     *     (default is equal to [])
     * @return bool
     */
    public function addLocaleStrings($language, $strings = [])
    {
        if(!is_array($strings) || empty($strings)) {
            $this->sError = "Translation strings empty or not an array";

            return false;
        }
        $dictionary = "";
        foreach($strings as $key => $value) {
            $dictionary .= '"'. $this->escapeLocaleString($key) .'" = "'. $this->escapeLocaleString($value) .'";'. PHP_EOL;
        }
        $this->locales[$language] = $dictionary;

        return true;
    }

    /**
     * Add a file to the file array.
     *
     * @param string $language language for which file to be added
     * @param string $path Path to file
     * @param string $name Filename to use in pass archive
     *     (default is equal to $path)
     * @return bool
     */
    public function addLocaleFile($language, $path, $name = null)
    {
        if(file_exists($path)) {
            $name = ($name === null) ? basename($path) : $name;
            $this->files[$language .'.lproj/'. $name] = $path;

            return true;
        }

        $this->sError = sprintf('File %s does not exist.', $path);

        return false;
    }

    /**
     * Add a file to the file array.
     *
     * @param string $path Path to file
     * @param string $name Filename to use in pass archive
     *     (default is equal to $path)
     * @return bool
     */
    public function addFile($path, $name = null)
    {
        if(file_exists($path)) {
            $name = ($name === null) ? basename($path) : $name;
            $this->files[$name] = $path;

            return true;
        }

        $this->sError = sprintf('File %s does not exist.', $path);

        return false;
    }

    /**
     * Add a file from a url to the remote file urls array.
     *
     * @param string $url URL to file
     * @param string $name Filename to use in pass archive
     *     (default is equal to $url)
     * @return bool
     */
    public function addRemoteFile($url, $name = null)
    {
      $name = ($name === null) ? basename($url) : $name;
      $this->remote_file_urls[$name] = $url;

      return true;
    }
    
    /**
     * Add a locale file from a url to the remote file urls array.
     *
     * @param string $language language for which file to be added
     * @param string $url URL to file
     * @param string $name Filename to use in pass archive
     *     (default is equal to $url)
     * @return bool
     */
    public function addLocaleRemoteFile($language, $url, $name = null)
    {
      $name = ($name === null) ? basename($url) : $name;
      $this->remote_file_urls[$language .'.lproj/'. $name] = $url;

      return true;
    }

    /**
     * Create the actual .pkpass file.
     *
     * @param bool $output Whether to output it directly or return the pass
     *     contents as a string.
     *
     * @return bool|string
     */
    public function create($output = false)
    {
        $paths = $this->getTempPaths();

        // Creates and saves the json manifest
        if(!($manifest = $this->createManifest())) {
            $this->clean();

            return false;
        }

        // Create signature
        if($this->createSignature($manifest) == false) {
            $this->clean();

            return false;
        }

        if($this->createZip($manifest) == false) {
            $this->clean();

            return false;
        }

        // Check if pass is created and valid
        if(!file_exists($paths['pkpass']) || filesize($paths['pkpass']) < 1) {
            $this->sError = 'Error while creating pass.pkpass. Check your ZIP extension.';
            $this->clean();

            return false;
        }

        // Get contents of generated file
        $file = file_get_contents($paths['pkpass']);
        $size = filesize($paths['pkpass']);
        $name = basename($paths['pkpass']);

        // Cleanup
        $this->clean();

        // Output pass
        if($output == true) {
            $fileName = $this->getName() ? $this->getName() : $name;
            if(!strstr($fileName, '.')) {
                $fileName .= '.pkpass';
            }
            header('Content-Description: File Transfer');
            header('Content-Type: application/vnd.apple.pkpass');
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
            header('Content-Transfer-Encoding: binary');
            header('Connection: Keep-Alive');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s T'));
            header('Pragma: public');
            if (ob_get_level() > 0)
            {
                ob_end_flush();
            }
            set_time_limit(0);
            echo $file;

            return true;
        }

        return $file;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param $error
     *
     * @return bool
     */
    public function checkError(&$error)
    {
        if(trim($this->sError) == '') {
            return false;
        }

        $error = $this->sError;

        return true;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->sError;
    }

    /**
     * Sub-function of create()
     * This function creates the hashes for the files and adds them into a json
     * string.
     */
    protected function createManifest()
    {
        // Creates SHA hashes for all files in package
        $this->shas['pass.json'] = sha1($this->json);

        // Creates SHA hashes for string files in each project.
        foreach($this->locales as $language => $strings) {
            $this->shas[$language. '.lproj/pass.strings'] = sha1($strings);
        }

        $has_icon = false;
        foreach($this->files as $name => $path) {
            if(strtolower($name) == 'icon.png') {
                $has_icon = true;
            }
            $this->shas[$name] = sha1(file_get_contents($path));
        }

        foreach($this->remote_file_urls as $name => $url) {
            if(strtolower($name) == 'icon.png') {
                $has_icon = true;
            }
            $this->shas[$name] = sha1(file_get_contents($url));
        }

        if(!$has_icon) {
            $this->sError = 'Missing required icon.png file.';
            $this->clean();

            return false;
        }

        $manifest = json_encode((object)$this->shas);

        return $manifest;
    }

    /**
     * Converts PKCS7 PEM to PKCS7 DER
     * Parameter: string, holding PKCS7 PEM, binary, detached
     * Return: string, PKCS7 DER.
     *
     * @param $signature
     *
     * @return string
     */
    protected function convertPEMtoDER($signature)
    {
        $begin = 'filename="smime.p7s"';
        $end = '------';
        $signature = substr($signature, strpos($signature, $begin) + strlen($begin));

        $signature = substr($signature, 0, strpos($signature, $end));
        $signature = trim($signature);
        $signature = base64_decode($signature);

        return $signature;
    }

    /**
     * Creates a signature and saves it
     * Parameter: json-string, manifest file
     * Return: boolean, true on success, false on failure.
     *
     * @param $manifest
     *
     * @return bool
     */
    protected function createSignature($manifest)
    {
        $paths = $this->getTempPaths();

        file_put_contents($paths['manifest'], $manifest);

        if(!$pkcs12 = file_get_contents($this->certPath)) {
            $this->sError = 'Could not read the certificate';

            return false;
        }

        $certs = [];
        if(!openssl_pkcs12_read($pkcs12, $certs, $this->certPass)) {
            $this->sError = 'Invalid certificate file. Make sure you have a ' .
                'P12 certificate that also contains a private key, and you ' .
                'have specified the correct password!';

            return false;
        }
        $certdata = openssl_x509_read($certs['cert']);
        $privkey = openssl_pkey_get_private($certs['pkey'], $this->certPass);

        $openssl_args = [
            $paths['manifest'],
            $paths['signature'],
            $certdata,
            $privkey,
            [],
            PKCS7_BINARY | PKCS7_DETACHED
        ];

        if(!empty($this->wwdrCertPath)) {
            if(!file_exists($this->wwdrCertPath)) {
                $this->sError = 'WWDR Intermediate Certificate does not exist';

                return false;
            }

            $openssl_args[] = $this->wwdrCertPath;
        }

        call_user_func_array('openssl_pkcs7_sign', $openssl_args);

        $signature = file_get_contents($paths['signature']);
        $signature = $this->convertPEMtoDER($signature);
        file_put_contents($paths['signature'], $signature);

        return true;
    }

    /**
     * Creates .pkpass (zip archive)
     * Parameter: json-string, manifest file
     * Return: boolean, true on succes, false on failure.
     *
     * @param $manifest
     *
     * @return bool
     */
    protected function createZip($manifest)
    {
        $paths = $this->getTempPaths();

        // Package file in Zip (as .pkpass)
        $zip = new ZipArchive();
        if(!$zip->open($paths['pkpass'], ZipArchive::CREATE)) {
            $this->sError = 'Could not open ' . basename($paths['pkpass']) . ' with ZipArchive extension.';

            return false;
        }
        $zip->addFile($paths['signature'], 'signature');
        $zip->addFromString('manifest.json', $manifest);
        $zip->addFromString('pass.json', $this->json);

        // Add transilation dictionary
        foreach($this->locales as $language => $strings) {
            if(!$zip->addEmptyDir($language . '.lproj')) {
                $this->sError = 'Could not create ' . $language . '.lproj folder in zip archive.';

                return false;
            }
            $zip->addFromString($language. '.lproj/pass.strings', $strings);
        }

        foreach($this->files as $name => $path) {
            $zip->addFile($path, $name);
        }

        foreach($this->remote_file_urls as $name => $url) {
            $download_file = file_get_contents($url);
            $zip->addFromString($name, $download_file);
        }
        $zip->close();

        return true;
    }

    /**
     * Declares all paths used for temporary files.
     */
    protected function getTempPaths()
    {
        // Declare base paths
        $paths = [
            'pkpass' => 'pass.pkpass',
            'signature' => 'signature',
            'manifest' => 'manifest.json',
        ];

        // If trailing slash is missing, add it
        if(substr($this->tempPath, -1) != '/') {
            $this->tempPath = $this->tempPath . '/';
        }

        // Generate a unique sub-folder in the tempPath to support generating more
        // passes at the same time without erasing/overwriting each others files
        if(empty($this->uniqid)) {
            $this->uniqid = uniqid('PKPass', true);
        }

        if(!is_dir($this->tempPath . $this->uniqid)) {
            mkdir($this->tempPath . $this->uniqid);
        }

        // Add temp folder path
        foreach($paths as $pathName => $path) {
            $paths[$pathName] = $this->tempPath . $this->uniqid . '/' . $path;
        }

        return $paths;
    }

    /**
     * Removes all temporary files.
     */
    protected function clean()
    {
        $paths = $this->getTempPaths();

        foreach($paths as $path) {
            if(file_exists($path)) {
                unlink($path);
            }
        }

        // Remove our unique temporary folder
        if(is_dir($this->tempPath . $this->uniqid)) {
            rmdir($this->tempPath . $this->uniqid);
        }

        return true;
    }

    protected static $escapeChars = [
        "\n" => "\\n",
        "\r" => "\\r",
        "\"" => "\\\"",
        "\\" => "\\\\"
    ];
    /**
     * Escapes strings for use in locale files
     */
    protected function escapeLocaleString($string) {
        return strtr($string, self::$escapeChars);
    }
}
