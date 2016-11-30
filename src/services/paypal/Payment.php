<?php

namespace services\paypal;

/**
 * Платеж в системе Paypal
 *
 * @author Ilya Kolesnikov <fatumm@gmail.com>
 * @package services\paypal
 */
class Payment
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var \DateTime
     */
    public $createdAt;

    /**
     * @var stgring
     */
    public $state;

    /**
     * @var string
     */
    public $description;

    /**
     * @var Item[]
     */
    public $items = [];
}