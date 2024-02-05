<?php

/**
 * Copyright (c) 2022, Includable.
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
    const FILE_TYPE = 'pass';
    const FILE_EXT = 'pkpass';
    const MIME_TYPE = 'application/vnd.apple.pkpass';
    /**
     * Holds the path to the certificate.
     * @var string
     */
    protected $certPath;

    /**
     * Name of the downloaded file.
     * @var string
     */
    protected $name;

    /**
     * Holds the files to include in the pass.
     * @var string[]
     */
    protected $files = [];

    /**
     * Holds the remote file urls to include in the pass.
     * @var string[]
     */
    protected $remote_file_urls = [];

    /**
     * Holds the files content to include in the pass.
     * @var string[]
     */
    protected $files_content = [];

    /**
     * Holds the JSON payload.
     * @var string
     */
    protected $json;

    /**
     * Holds the password to the certificate.
     * @var string
     */
    protected $certPass = '';

    /**
     * Holds the path to the WWDR Intermediate certificate.
     * @var string
     */
    protected $wwdrCertPath = '';

    /**
     * Holds the path to a temporary folder. Defaults to the system temp directory.
     * @var string
     */
    protected $tempPath;

    /**
     * Holds array of localization details.
     * @var array
     */
    protected $locales = [];

    /**
     * PKPass constructor.
     *
     * @param string|bool $certificatePath
     * @param string|bool $certificatePassword
     */
    public function __construct($certificatePath = null, $certificatePassword = null)
    {
        $this->tempPath = sys_get_temp_dir();
        $this->wwdrCertPath = __DIR__ . '/Certificate/AppleWWDRCA.pem';

        if ($certificatePath) {
            $this->setCertificatePath($certificatePath);
        }
        if ($certificatePassword) {
            $this->setCertificatePassword($certificatePassword);
        }
    }

    /**
     * Sets the path to a certificate
     * Parameter: string, path to certificate
     * Return: boolean, always true.
     *
     * @param string $path
     *
     * @return bool
     */
    public function setCertificatePath($path)
    {
        $this->certPath = $path;

        return true;
    }

    /**
     * Sets the certificate's password
     * Parameter: string, password to the certificate
     * Return: boolean, always true.
     *
     * @param string $password
     *
     * @return bool
     */
    public function setCertificatePassword($password)
    {
        $this->certPass = $password;

        return true;
    }

    /**
     * Sets the path to the WWDR Intermediate certificate
     * Parameter: string, path to certificate
     * Return: boolean, always true.
     *
     * @param string $path
     *
     * @return bool
     */
    public function setWwdrCertificatePath($path)
    {
        $this->wwdrCertPath = $path;

        return true;
    }

    /**
     * Set the path to the temporary directory.
     *
     * @param string $path Path to temporary directory
     */
    public function setTempPath($path)
    {
        $this->tempPath = $path;
    }

    /**
     * Set pass data.
     *
     * @param string|array|object $data
     * @throws PKPassException
     */
    public function setData($data)
    {
        // Array is passed as input
        if (is_array($data) || is_object($data)) {
            $this->json = json_encode($data);
            return;
        }

        // JSON string is passed as input
        if (json_decode($data) !== false) {
            $this->json = $data;
            return;
        }

        throw new PKPassException('Invalid data passed to setData: this is not a JSON string.');
    }

    /**
     * Add dictionary of strings for translation.
     *
     * @param string $language language project need to be added
     * @param array $strings a key value pair of translation strings (default is equal to [])
     * @throws PKPassException
     */
    public function addLocaleStrings($language, $strings = [])
    {
        if (!is_array($strings) || empty($strings)) {
            throw new PKPassException('Translation strings empty or not an array.');
        }

        $dictionary = "";
        foreach ($strings as $key => $value) {
            $dictionary .= '"' . $this->escapeLocaleString($key) . '" = "' . $this->escapeLocaleString($value) . '";' . PHP_EOL;
        }
        $this->locales[$language] = $dictionary;
    }

    /**
     * Add a localized file to the file array.
     *
     * @param string $language language for which file to be added
     * @param string $path Path to file
     * @param string $name Filename to use in pass archive (default is equal to $path)
     * @throws PKPassException
     */
    public function addLocaleFile($language, $path, $name = null)
    {
        if (!file_exists($path)) {
            throw new PKPassException(sprintf('File %s does not exist.', $path));
        }

        $name = $name ?: basename($path);
        $this->files[$language . '.lproj/' . $name] = $path;
    }

    /**
     * Add a file to the file array.
     *
     * @param string $path Path to file
     * @param string $name Filename to use in pass archive (default is equal to $path)
     * @throws PKPassException
     */
    public function addFile($path, $name = null)
    {
        if (!file_exists($path)) {
            throw new PKPassException(sprintf('File %s does not exist.', $path));
        }

        $name = $name ?: basename($path);
        $this->files[$name] = $path;

        return false;
    }

    /**
     * Add a file from a url to the remote file urls array.
     *
     * @param string $url URL to file
     * @param string $name Filename to use in pass archive (default is equal to $url)
     */
    public function addRemoteFile($url, $name = null)
    {
        $name = $name ?: basename($url);
        $this->remote_file_urls[$name] = $url;
    }

    /**
     * Add a locale file from a url to the remote file urls array.
     *
     * @param string $language language for which file to be added
     * @param string $content Content of file
     * @param string $name Filename to use in pass archive (default is equal to $url)
     */
    public function addLocaleRemoteFile($language, $url, $name = null)
    {
        $name = $name ?: basename($url);
        $this->remote_file_urls[$language . '.lproj/' . $name] = $url;
    }

    /**
     * Add a file from a string to the string files array.
     *
     * @param string $content Content of file
     * @param string $name Filename to use in pass archive (default is equal to $url)
     */
    public function addFileContent($content, $name)
    {
        $this->files_content[$name] = $content;
    }

    /**
     * Add a locale file from a string to the string files array.
     *
     * @param string $language language for which file to be added
     * @param string $content Content of file
     * @param string $name Filename to use in pass archive (default is equal to $url)
     */
    public function addLocaleFileContent($language, $content, $name)
    {
        $this->files_content[$language . '.lproj/' . $name] = $content;
    }

    /**
     * Create the actual .pkpass file.
     *
     * @param bool $output Whether to output it directly or return the pass contents as a string.
     *
     * @return string
     * @throws PKPassException
     */
    public function create($output = false)
    {
        // Prepare payload
        $manifest = $this->createManifest();
        $signature = $this->createSignature($manifest);

        // Build ZIP file
        $zip = $this->createZip($manifest, $signature);

        // Return pass
        if (!$output) {
            return $zip;
        }

        // Output pass
        header('Content-Description: File Transfer');
        header('Content-Type: ' . self::MIME_TYPE);
        header('Content-Disposition: attachment; filename="' . $this->getName() . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: Keep-Alive');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s T'));
        header('Pragma: public');
        echo $zip;

        return '';
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->name ?: self::FILE_TYPE;
        if (!strstr($name, '.')) {
            $name .= '.' . self::FILE_EXT;
        }

        return $name;
    }

    /**
     * Set filename.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Sub-function of create()
     * This function creates the hashes for the files and adds them into a json string.
     *
     * @throws PKPassException
     */
    protected function createManifest()
    {
        // Creates SHA hashes for all files in package
        $sha = [];
        $sha['pass.json'] = sha1($this->json);

        // Creates SHA hashes for string files in each project.
        foreach ($this->locales as $language => $strings) {
            $sha[$language . '.lproj/pass.strings'] = sha1($strings);
        }

        $has_icon = false;
        foreach ($this->files as $name => $path) {
            if (strtolower($name) == 'icon.png') {
                $has_icon = true;
            }
            $sha[$name] = sha1(file_get_contents($path));
        }

        foreach ($this->remote_file_urls as $name => $url) {
            if (strtolower($name) == 'icon.png') {
                $has_icon = true;
            }
            $sha[$name] = sha1(file_get_contents($url));
        }

        foreach ($this->files_content as $name => $content) {
            if (strtolower($name) == 'icon.png') {
                $has_icon = true;
            }
            $sha[$name] = sha1($content);
        }

        if (!$has_icon) {
            throw new PKPassException('Missing required icon.png file.');
        }

        return json_encode((object)$sha);
    }

    /**
     * Converts PKCS7 PEM to PKCS7 DER
     * Parameter: string, holding PKCS7 PEM, binary, detached
     * Return: string, PKCS7 DER.
     *
     * @param string $signature
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

        return base64_decode($signature);
    }

    /**
     * Read a PKCS12 certificate string and turn it into an array.
     *
     * @return array
     * @throws PKPassException
     */
    protected function readP12()
    {
        // Use the built-in reader first
        if (!$pkcs12 = file_get_contents($this->certPath)) {
            throw new PKPassException('Could not read the certificate.');
        }
        $certs = [];
        if (openssl_pkcs12_read($pkcs12, $certs, $this->certPass)) {
            return $certs;
        }

        // That failed, let's check why
        $error = '';
        while ($text = openssl_error_string()) {
            $error .= $text;
        }

        // General error
        if (!strstr($error, 'digital envelope routines::unsupported')) {
            throw new PKPassException(
                'Invalid certificate file. Make sure you have a ' .
                'P12 certificate that also contains a private key, and you ' .
                'have specified the correct password!' . PHP_EOL . PHP_EOL .
                'OpenSSL error: ' . $error
            );
        }

        // Try an alternative route using shell_exec
        try {
            $value = @shell_exec(
                "openssl pkcs12 -in " . escapeshellarg($this->certPath) .
                " -passin " . escapeshellarg("pass:" . $this->certPass) .
                " -passout " . escapeshellarg("pass:" . $this->certPass) .
                " -legacy"
            );
            if ($value) {
                $cert = substr($value, strpos($value, '-----BEGIN CERTIFICATE-----'));
                $cert = substr($cert, 0, strpos($cert, '-----END CERTIFICATE-----') + 25);
                $key = substr($value, strpos($value, '-----BEGIN ENCRYPTED PRIVATE KEY-----'));
                $key = substr($key, 0, strpos($key, '-----END ENCRYPTED PRIVATE KEY-----') + 35);
                if (strlen($cert) > 0 && strlen($key) > 0) {
                    $certs['cert'] = $cert;
                    $certs['pkey'] = $key;
                    return $certs;
                }
            }
        } catch (\Throwable $e) {
            // no need to do anything
        }

        throw new PKPassException(
            'Could not read certificate file. This might be related ' .
            'to using an OpenSSL version that has deprecated some older ' .
            'hashes. More info here: https://schof.link/2Et6z3m ' . PHP_EOL . PHP_EOL .
            'OpenSSL error: ' . $error
        );
    }

    /**
     * Creates a signature and saves it.
     *
     * @param string $manifest
     * @throws PKPassException
     */
    protected function createSignature($manifest)
    {
        $manifest_path = tempnam($this->tempPath, 'pkpass');
        $signature_path = tempnam($this->tempPath, 'pkpass');
        file_put_contents($manifest_path, $manifest);

        $certs = $this->readP12();
        $certdata = openssl_x509_read($certs['cert']);
        $privkey = openssl_pkey_get_private($certs['pkey'], $this->certPass);

        $openssl_args = [
            $manifest_path,
            $signature_path,
            $certdata,
            $privkey,
            [],
            PKCS7_BINARY | PKCS7_DETACHED
        ];

        if (!empty($this->wwdrCertPath)) {
            if (!file_exists($this->wwdrCertPath)) {
                throw new PKPassException('WWDR Intermediate Certificate does not exist.');
            }

            $openssl_args[] = $this->wwdrCertPath;
        }

        call_user_func_array('openssl_pkcs7_sign', $openssl_args);

        $signature = file_get_contents($signature_path);
        unlink($manifest_path);
        unlink($signature_path);

        return $this->convertPEMtoDER($signature);
    }

    /**
     * Creates .pkpass zip archive.
     *
     * @param string $manifest
     * @param string $signature
     * @return string
     * @throws PKPassException
     */
    protected function createZip($manifest, $signature)
    {
        // Package file in Zip (as .pkpass)
        $zip = new ZipArchive();
        $filename = tempnam($this->tempPath, 'pkpass');
        if (!$zip->open($filename, ZipArchive::OVERWRITE)) {
            throw new PKPassException('Could not open ' . basename($filename) . ' with ZipArchive extension.');
        }
        $zip->addFromString('signature', $signature);
        $zip->addFromString('manifest.json', $manifest);
        $zip->addFromString('pass.json', $this->json);

        // Add translation dictionary
        foreach ($this->locales as $language => $strings) {
            if (!$zip->addEmptyDir($language . '.lproj')) {
                throw new PKPassException('Could not create ' . $language . '.lproj folder in zip archive.');
            }
            $zip->addFromString($language . '.lproj/pass.strings', $strings);
        }

        foreach ($this->files as $name => $path) {
            $zip->addFile($path, $name);
        }

        foreach ($this->remote_file_urls as $name => $url) {
            $download_file = file_get_contents($url);
            $zip->addFromString($name, $download_file);
        }

        foreach ($this->files_content as $name => $content) {
            $zip->addFromString($name, $content);
        }

        $zip->close();

        if (!file_exists($filename) || filesize($filename) < 1) {
            @unlink($filename);
            throw new PKPassException('Error while creating pass.pkpass. Check your ZIP extension.');
        }

        $content = file_get_contents($filename);
        unlink($filename);

        return $content;
    }

    protected static $escapeChars = [
        "\n" => "\\n",
        "\r" => "\\r",
        "\"" => "\\\"",
        "\\" => "\\\\"
    ];

    /**
     * Escapes strings for use in locale files
     * @param string $string
     * @return string
     */
    protected function escapeLocaleString($string)
    {
        return strtr($string, self::$escapeChars);
    }
}
