<?php

$I = new FunctionalTester($scenario);
$I->wantTo('update inventory quantity of specific item');

$keyId = '<Access key ID here>';
$secret = '<Secret here>';
$appName = '<Application name here>';
$appVersion = '<Application version here>';
$merchantId = '<Your merchant ID here>';

$service = new \services\Amazon($keyId, $secret, $merchantId);
$service->setAppName($appName);
$service->setAppVersion($appVersion);

$data = [
    19719 => 0,
];

$result = $service->submitFeed($data);

\PHPUnit_Framework_Assert::assertTrue($result);