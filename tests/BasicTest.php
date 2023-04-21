<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PKPass\PKPass;

final class BasicTest extends TestCase
{
    private function validatePass($pass, $expected_files = [])
    {
        // basic string validation
        $this->assertIsString($pass);
        $this->assertGreaterThan(100, strlen($pass));
        $this->assertStringContainsString('icon.png', $pass);
        $this->assertStringContainsString('manifest.json', $pass);

        // try to read the ZIP file
        $temp_name = tempnam(sys_get_temp_dir(), 'pkpass');
        file_put_contents($temp_name, $pass);
        $zip = new ZipArchive();
        $res = $zip->open($temp_name);
        $this->assertTrue($res, 'Invalid ZIP file.');
        $this->assertEquals(count($expected_files), $zip->numFiles);

        // extract zip to temp dir
        $temp_dir = $temp_name . '_dir';
        mkdir($temp_dir);
        $zip->extractTo($temp_dir);
        $zip->close();
        echo $temp_dir;
        foreach ($expected_files as $file) {
            $this->assertFileExists($temp_dir . DIRECTORY_SEPARATOR . $file);
        }
    }

    public function testBasicGeneration()
    {
        $pass = new PKPass(__DIR__ . '/fixtures/example-certificate.p12', 'password');
        $data = [
            'description' => 'Demo pass',
            'formatVersion' => 1,
            'organizationName' => 'Flight Express',
            'passTypeIdentifier' => 'pass.com.scholica.flights', // Change this!
            'serialNumber' => '12345678',
            'teamIdentifier' => 'KN44X8ZLNC', // Change this!
            'barcode' => [
                'format' => 'PKBarcodeFormatQR',
                'message' => 'Flight-GateF12-ID6643679AH7B',
                'messageEncoding' => 'iso-8859-1',
            ],
            'backgroundColor' => 'rgb(32,110,247)',
            'logoText' => 'Flight info',
            'relevantDate' => date('Y-m-d\TH:i:sP')
        ];
        $pass->setData($data);
        $pass->addFile(__DIR__ . '/fixtures/icon.png');
        $value = $pass->create();

        $this->validatePass($value, [
            'icon.png',
            'manifest.json',
            'pass.json',
            'signature',
        ]);
    }
}