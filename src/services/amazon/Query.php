<?php

namespace services\amazon;

/**
 * Объект запроса к API Amazon MWS
 *
 * @author Ilya Kolesnikov <fatumm@gmail.com>
 * @package services\amazon
 */
class Query
{
    const XSI_LOC = 'amzn-envelope.xsd';
    const XSI_URL = 'http://www.w3.org/2001/XMLSchema-instance';

    const MESSAGE_TYPE_INVENTORY = 'Inventory';

    const OPERATION_TYPE_UPDATE = 'Update';

    const PART_TYPE = 'OperationType';
    const PART_ID = 'MessageID';
    const PART_ARGS = 'Args';

    /**
     * @var string
     */
    private $docVersion;

    /**
     * @var string
     */
    private $merchantId;

    /**
     * @var array
     */
    private $messages = array();

    /**
     * @var string
     */
    private $messageType;

    /**
     * @var int
     */
    private static $messageCount = 1;

    /**
     * @param string $merchantId
     * @param string $docVersion
     */
    private function __construct($merchantId, $docVersion)
    {
        $this->docVersion = $docVersion;
        $this->merchantId = $merchantId;
    }

    /**
     * @param string $merchantId
     * @param string $docVersion
     * @return Query
     */
    public static function createInventory($merchantId, $docVersion = '1.01')
    {
        $query = new self($merchantId, $docVersion);
        $query->setMessageType(self::MESSAGE_TYPE_INVENTORY);

        return $query;
    }

    /**
     * @param string $messageType
     */
    public function setMessageType($messageType)
    {
        $this->messageType = $messageType;
    }

    /**
     * @param string $sku
     * @param int $quantity
     * @return Query
     */
    public function inventory($sku, $quantity)
    {
        $this->messages[] = array(
            self::PART_TYPE => self::OPERATION_TYPE_UPDATE,
            self::PART_ID => self::$messageCount,
            self::PART_ARGS => array(
                'SKU' => $sku,
                'Quantity' => $quantity,
            ),
        );

        self::$messageCount++;

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $out = $this->renderSchema();

        $out = strtr(
            $out,
            array(
                '{header}' => $this->renderHeader(),
                '{messageType}' => $this->renderMessageType(),
                '{messages}' => $this->renderMessages(),
            )
        );

        return $out;
    }

    /**
     * @return string
     */
    private function renderSchema()
    {
        return
            '<?xml version="1.0" encoding="utf-8" ?>' .
            '<AmazonEnvelope xmlns:xsi="' . self::XSI_URL . '" xsi:noNamespaceSchemaLocation="' . self::XSI_LOC . '">' .
            '{header}' .
            '{messageType}' .
            '{messages}' .
            '</AmazonEnvelope>';
    }

    /**
     * @return string
     */
    private function renderHeader()
    {
        return
            '<Header>' .
            '<DocumentVersion>' . $this->docVersion . '</DocumentVersion>' .
            '<MerchantIdentifier>' . $this->merchantId . '</MerchantIdentifier>' .
            '</Header>';

    }

    /**
     * @return string
     */
    private function renderMessageType()
    {
        return '<MessageType>' . $this->messageType. '</MessageType>';
    }

    /**
     * @return string
     */
    private function renderMessages()
    {
        $messages = array();

        foreach ($this->messages as $message) {
            $out = $this->renderMessageHeader($message);
            $methodName = 'render' . $this->messageType;
            $out = str_replace('{message}', $this->$methodName($message), $out);
            $messages[] = $out;
        }

        return implode('', $messages);
    }

    /**
     * @param array $message
     * @return string
     */
    private function renderMessageHeader($message)
    {
        return
            '<Message>' .
            '<MessageID>' . $message[self::PART_ID] . '</MessageID>' .
            '<OperationType>' . $message[self::PART_TYPE] . '</OperationType>' .
            '{message}' .
            '</Message>';
    }

    /**
     * @param array $message
     * @return string
     */
    private function renderInventory($message)
    {
        return
            '<Inventory>' .
            '<SKU>' . $message[self::PART_ARGS]['SKU'] . '</SKU>' .
            '<Quantity>' . $message[self::PART_ARGS]['Quantity'] . '</Quantity>' .
            '</Inventory>';
    }
}