<?php
// Main App File

require 'vendor/autoload.php';

use src\Commissions;

if($argv[1]) {
    $commissions = new Commissions();
    $resultCommissions = $commissions->getCommissions($argv[1]);
    foreach ($resultCommissions as $commission) {
        echo $commission . "\n";
    }
}