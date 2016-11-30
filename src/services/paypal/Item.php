<?php

namespace services\paypal;

/**
 * Товар в системе Paypal
 *
 * @author Ilya Kolesnikov <fatumm@gmail.com>
 * @package services\paypal
 */
class Item
{
    /**
     * @var string
     */
    public $sku;

    /**
     * @var string
     */
    public $name;

    /**
     * @var int
     */
    public $quantity;

    /**
     * @var float
     */
    public $price;

    /**
     * @var string
     */
    public $currency;
}