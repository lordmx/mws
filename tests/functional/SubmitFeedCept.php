<?php

$I = new FunctionalTester($scenario);
$I->wantTo('update inventory quantity of specific item');

$keyId = 'AKIAIRTGHSC6CNEYSFBA';
$secret = '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';
$appName = 'Eyewear-Emporium';
$appVersion = '6852-7963-9778';
$merchantId = 'A2J7RWW4J59FEO';

$service = new \services\Amazon($keyId, $secret, $merchantId);
$service->setAppName($appName);
$service->setAppVersion($appVersion);
$service->setMarketplaceIds(array('ATVPDKIKX0DER'));

// _POST_INVENTORY_AVAILABILITY_DATA_

$data = [
    23705 => 9,
];

$result = $service->submitFeed($data);

\PHPUnit_Framework_Assert::assertTrue($result);