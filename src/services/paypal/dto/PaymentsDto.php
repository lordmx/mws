<?php

namespace services\paypal;

/**
 * DTO, используемое как набор критериев для получения списка платежей Paypal
 *
 * @author Ilya Kolesnikov <fatumm@gmail.com>
 * @package services\paypal
 */
class PaymentsDto
{
    /**
     * @var int
     */
    public $count;

    /**
     * @var string
     */
    public $startId;

    /**
     * @var int
     */
    public $startIndex;

    /**
     * @var \DateTime
     */
    public $startTime;

    /**
     * @var \DateTime
     */
    public $endTime;

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        if ($this->count) {
            $result['count'] = (int)$this->count;
        }

        if ($this->startId) {
            $result['start_id'] = $this->startId;
        }

        if ($this->startIndex) {
            $result['start_index'] = (int)$this->startIndex;
        }

        if ($this->startTime) {
            $result['start_time'] = $this->startTime->format(DATE_W3C);
        }

        if ($this->endTime) {
            $result['end_time'] = $this->endTime->format(DATE_W3C);
        }

        return $result;
    }
}