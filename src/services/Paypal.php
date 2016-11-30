<?php

namespace services;

use PayPal\Auth\OAuthTokenCredential;
use PayPal\Rest\ApiContext;
use services\paypal\mappers\MapperInterface;
use services\paypal\mappers\MapperSimple;
use services\paypal\Payment;

/**
 * Сервис для работы с платежами paypal
 *
 * @author Ilya Kolesnikov <fatumm@gmail.com>
 * @package services
 */
class Paypal
{
    /**
     * @var \Paypal\Rest\ApiContext
     */
    private $context;

    /**
     * @var MapperInterface
     */
    private $mapper;

    /**
     * @param string $clientId
     * @param string $secret
     * @param string $mode
     */
    public function __construct($clientId, $secret, $mode = 'live')
    {
        $this->context = new ApiContext(new OAuthTokenCredential($clientId, $secret));
        $config = array(
            'mode' => $mode,
        );

        $this->context->setConfig($config);
    }

    /**
     * Получить список платежей
     *
     * @param PaymentsDto $paymentsDto
     * @return Payment[]
     */
    public function getPayments(PaymentsDto $paymentsDto)
    {
        $result = [];

        try {
            $payments = \PayPal\Api\Payment::all($paymentsDto->toArray(), $this->context);
        } catch (\Exception $e) {
            return $result;
        }

        foreach ($payments as $payment) {
            $result[] = $this->getMapper()->map($payment);
        }

        return $result;
    }

    /**
     * @return MapperInterface
     */
    private function getMapper()
    {
        if (!$this->mapper) {
            $this->mapper = new MapperSimple();
        }

        return $this->mapper;
    }

    /**
     * @param MapperInterface $mapper
     */
    public function setMapper(MapperInterface $mapper)
    {
        $this->mapper = $mapper;
    }
}