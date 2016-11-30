<?php

namespace services\paypal\mappers;

use PayPal\Api\Payment;
use services\paypal\Item;

/**
 * @author Ilya Kolesnikov <fatumm@gmail.com>
 * @package services\paypal\mappers
 */
class MapperSimple implements MapperInterface
{
    /**
     * @inheritdoc
     */
    public function map(Payment $payment)
    {
        $result = new \services\paypal\Payment();

        $result->id = $payment->id;
        $result->createdAt = new \DateTime($payment->create_time);
        $result->state = $payment->state;

        $transaction = reset($payment->transactions);
        $result->description = $transaction->description;

        foreach ($transaction->item_list->items as $transactionItem) {
            $item = new Item();
            $item->sku = $transactionItem->sku;
            $item->name = $transactionItem->name;
            $item->price = $transactionItem->price;
            $item->quantity = $transactionItem->quantity;
            $item->currency = $transactionItem->currency;

            $result->items[] = $item;
        }

        return $result;
    }
}