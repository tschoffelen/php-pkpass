<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PKPass\FinanceOrder;

final class FinanceOrderTest extends TestCase
{
    private function validateOrder($orer, $expected_files = [])
    {
        // basic string validation
        $this->assertIsString($orer);
        $this->assertGreaterThan(100, strlen($orer));
        $this->assertStringContainsString('logo.png', $orer);
        $this->assertStringContainsString('ws03-xs-red.jpg', $orer);
        $this->assertStringContainsString('manifest.json', $orer);

        // try to read the ZIP file
        $temp_name = tempnam(sys_get_temp_dir(), 'pkpass');
        file_put_contents($temp_name, $orer);
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
        $pass = new FinanceOrder(__DIR__ . '/fixtures/example-certificate.p12', 'password');
        $pass->setData([
            "createdAt" => "2024-02-01T19:45:50+00:00",
            "merchant" => [
                "displayName" => "Luma",
                "merchantIdentifier" => "merchant.com.pkpass.unit-test",
                "url" => "https://demo-store.test/",
                'logo' => 'logo.png',
            ],
            "orderIdentifier" => "1",
            "orderManagementURL" => "https://demo-store.test/sales/order/view",
            'orderNumber' => '#000000001',
            "orderType" => "ecommerce",
            "orderTypeIdentifier" => "order.com.pkpass.unit-test",
            'payment' => [
                'summaryItems' => [
                    [
                        'label' => 'Shipping & Handling',
                        'value' => [
                            'amount' => '5.00',
                            'currency' => 'USD',
                        ]
                    ],
                ],
                'total' => [
                    'amount' => '36.39',
                    'currency' => 'USB',
                ],
                'status' => 'paid'
            ],
            "status" => "open",
            "updatedAt" => "2024-02-01T19:45:50+00:00",
            'customer' => [
                'emailAddress' => 'roni_cost@example.com',
                'familyName' => 'Veronica',
                'givenName' => 'Costello',
            ],
            'lineItems' => [
                [
                    'image' => 'ws03-xs-red.jpg',
                    'price' => [
                        'amount' => '31.39',
                        'currency' => 'USD',
                    ],
                    'quantity' => 1,
                    'title' => 'Iris Workout Top',
                    'sku' => 'WS03-XS-Red',
                ],
            ],
            "schemaVersion" => 1,
        ]);
        $pass->addFile(__DIR__ . '/fixtures/order/logo.png');
        $pass->addFile(__DIR__ . '/fixtures/order/ws03-xs-red.jpg');
        $value = $pass->create();
        $this->validateOrder($value, [
            'logo.png',
            'ws03-xs-red.jpg',
            'manifest.json',
            'order.json',
            'signature',
        ]);
    }
}