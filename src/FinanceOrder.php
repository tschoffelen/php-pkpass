<?php declare(strict_types=1);

/**
 * Copyright (c), Includable.
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

class FinanceOrder extends PKPass
{
    const FILE_TYPE = 'order';
    const FILE_EXT = 'order';
    const MIME_TYPE = 'application/vnd.apple.finance.order';
    const PAYLOAD_FILE = 'order.json';
    const HASH_ALGO = 'sha256';

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
        $sha[self::PAYLOAD_FILE] = hash(self::HASH_ALGO, $this->json);

        // Creates SHA hashes for string files in each project.
        foreach ($this->locales as $language => $strings) {
            $sha[$language . '.lproj/' . self::FILE_TYPE . '.strings'] = hash(self::HASH_ALGO, $strings);
        }

        foreach ($this->files as $name => $path) {
            $sha[$name] = hash(self::HASH_ALGO, file_get_contents($path));
        }

        foreach ($this->remote_file_urls as $name => $url) {
            $sha[$name] = hash(self::HASH_ALGO, file_get_contents($url));
        }

        foreach ($this->files_content as $name => $content) {
            $sha[$name] = hash(self::HASH_ALGO, $content);
        }

        return json_encode((object)$sha);
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
        // Package file in Zip (as .order)
        $zip = new ZipArchive();
        $filename = tempnam($this->tempPath, self::FILE_TYPE);
        if (!$zip->open($filename, ZipArchive::OVERWRITE)) {
            throw new PKPassException('Could not open ' . basename($filename) . ' with ZipArchive extension.');
        }
        $zip->addFromString('signature', $signature);
        $zip->addFromString('manifest.json', $manifest);
        $zip->addFromString(self::PAYLOAD_FILE, $this->json);

        // Add translation dictionary
        foreach ($this->locales as $language => $strings) {
            if (!$zip->addEmptyDir($language . '.lproj')) {
                throw new PKPassException('Could not create ' . $language . '.lproj folder in zip archive.');
            }
            $zip->addFromString($language . '.lproj/' . self::FILE_TYPE . '.strings', $strings);
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
            throw new PKPassException('Error while creating order.order. Check your ZIP extension.');
        }

        $content = file_get_contents($filename);
        unlink($filename);

        return $content;
    }
}
