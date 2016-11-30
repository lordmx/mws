<?php

namespace services\paypal\mappers;

use PayPal\Api\Payment;

/**
 * Интерфейс маппера платежей из \PayPal\Api\Payment d services\paypal\Payment
 *
 * @author Ilya Kolesnikov <fatumm@gmail.com>
 * @package services\paypal\mappers
 */
interface MapperInterface
{
    /**
     * @param Payment $payment
     * @return \services\paypal\Payment
     */
    public function map(Payment $payment);
}