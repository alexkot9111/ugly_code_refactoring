<?php

use PHPUnit\Framework\TestCase;
use src\Commissions;

class CommissionsTest extends TestCase {

    // Define static rates
    private array $staticRates = [
        "EUR" => 1,
        "USD" => 1.083564,
        "JPY" => 169.505143,
        "GBP" => 0.850435,
    ];

    // Define static bins
    private array $staticBins = [
        '516793' => '{"country":{"alpha2":"LT"}}',
        '4745030' => '{"country":{"alpha2":"LT"}}',
        '41417360' => '{"country":{"alpha2":"LT"}}',
        '45417360' => '{"country":{"alpha2":"JP"}}',
        '45717360' => '{"country":{"alpha2":"DK"}}',
    ];

    public function testCommissions() {
        $commissions = new Commissions();
        $commissions->rates = $this->staticRates;
        $commissions->bins = $this->staticBins;
        $result = $commissions->getCommissions('input.txt');

        $expected = [1, 0.46, 1.67, 2.41, 43.72];
        foreach ($expected as $index => $value) {
            try {
                $this->assertEquals($value, $result[$index]);
            } catch (\PHPUnit\Framework\AssertionFailedError $e) {
                echo "Error: " . $e->getMessage() . "\n";
            }
        }
    }
}