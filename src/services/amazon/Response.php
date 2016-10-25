<?php

namespace services\amazon;

/**
 * @author Ilya Kolesnikov <fatumm@gmail.com>
 * @package services\amazon
 */
class Response
{
    /**
     * @var string
     */
    public $xml;

    /**
     * @var string
     */
    public $status;

    /**
     * @var string
     */
    public $requestId;

    /**
     * @var string
     */
    public $message;
}