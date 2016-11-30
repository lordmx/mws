<?php

$I = new FunctionalTester($scenario);
$I->wantTo('update inventory quantity of specific item');

$keyId = '';
$secret = '';
$appName = '';
$appVersion = '';
$merchantId = '';

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
