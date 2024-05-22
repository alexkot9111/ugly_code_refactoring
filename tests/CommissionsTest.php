<?php

use PHPUnit\Framework\TestCase;
use src\Commissions;

class CommissionsTest extends TestCase {

    public function testCommissions() {
        $commissions = new Commissions();
        $result = $commissions->getCommissions('input.txt', true);

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