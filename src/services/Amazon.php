<?php

namespace services;

use services\amazon\Response;
use services\exceptions\IntegrityException;
use services\amazon\Query;

/**
 * Сервис для работы с Amazon MWS
 *
 * @author Ilya Kolesnikov <fatumm@gmail.com>
 */
class Amazon
{
    const APP_DEFAULT_NAME = 'test';
    const APP_DEFAULT_VERSION = '0.0.1';
    const DOCUMENT_VERSION = '1.01';

    /**
     * @var \MarketplaceWebService_Client
     */
    private $client;

    /**
     * Идентификатор магазина в системе Amazon MWS
     *
     * @var string
     */
    private $merchantId;

    /**
     * URL эндпоинта API Amazon MWS (с учетом географического расположения)
     *
     * @var string
     */
    private $serviceUrl = 'https://mws.amazonservices.com';

    /**
     * URL proxy, через которую будет сделан запрос до API
     *
     * @var string
     */
    private $proxyHost;

    /**
     * Порт proxy
     *
     * @var int
     */
    private $proxyPort;

    /**
     * Название приложения (используется как часть заголовка User-Agent)
     *
     * @var string
     */
    private $appName;

    /**
     * Версия или билд приложения (используется как часть заголовка User-Agent)
     *
     * @var string
     */
    private $appVersion;

    /**
     * Максимальное количество попыток запроса (в случае ошибок)
     *
     * @var int
     */
    private $maxRetryCount = 3;

    /**
     * @param string $keyId
     * @param string $secret
     * @param string $merchantId
     */
    public function __construct($keyId, $secret, $merchantId)
    {
        $this->merchantId = $merchantId;
        $this->client = new \MarketplaceWebService_Client(
            $keyId,
            $secret,
            $this->getConfig(),
            $this->appName ?: self::APP_DEFAULT_NAME,
            $this->appVersion ?: self::APP_DEFAULT_VERSION
        );
    }

    /**
     * @param array $data Массив вида sku => quantity
     * @return Response|bool
     */
    public function submitFeed(array $data)
    {
        $query = Query::createInventory($this->merchantId, self::DOCUMENT_VERSION);

        foreach ($data as $sku => $quantity) {
            $query->inventory($sku, $quantity);
        }

        $xml = $query->render();

        $feedHandle = @fopen('php://temp', 'rw+');
        fwrite($feedHandle, $xml);
        rewind($feedHandle);

        $request = new \MarketplaceWebService_Model_SubmitFeedRequest();
        $request->setMerchant($this->merchantId);
        $request->setMarketplaceIdList($this->getMarketplaceIds());
        $request->setFeedType('_POST_FLAT_FILE_INVLOADER_DATA_');
        $request->setPurgeAndReplace(false);
        $request->setFeedContent($feedHandle);
        $request->setContentMd5(base64_encode(md5($xml)));

        try {
            $response = $this->client->submitFeed($request);

            if ($response->isSetSubmitFeedResult()) {
                $submitFeedResult = $response->getSubmitFeedResult();

                if ($submitFeedResult->isSetFeedSubmissionInfo()) {
                    return true;
                }
            }
        } catch (\MarketplaceWebService_Exception $e) {
            $response = new Response();
            $response->requestId = $e->getRequestId();
            $response->xml = $e->getXML();
            $response->status = $e->getStatusCode();
            $response->message = $e->getMessage();

            return $response;
        }

        return false;
    }

    /**
     * @param string $appName
     * @return Amazon
     */
    public function setAppName($appName)
    {
        $this->appName = $appName;
        return $this;
    }

    /**
     * @param string $appVersion
     * @return Amazon
     */
    public function setAppVersion($appVersion)
    {
        $this->appVersion = $appVersion;
        return $this;
    }

    /**
     * @param string $serviceUrl
     * @return Amazon
     */
    public function setServiceUrl($serviceUrl)
    {
        $this->serviceUrl = $serviceUrl;
        return $this;
    }

    /**
     * @param string $proxyHost
     * @return Amazon
     */
    public function setProxyHost($proxyHost)
    {
        $this->proxyHost = $proxyHost;
        return $this;
    }

    /**
     * @param int $proxyPort
     * @return Amazon
     */
    public function setProxyPort($proxyPort)
    {
        $this->proxyPort = $proxyPort;
        return $this;
    }

    /**
     * @param int $maxRetryCount
     * @return Amazon
     */
    public function setMaxRetryCount($maxRetryCount)
    {
        $this->maxRetryCount = $maxRetryCount;
        return $this;
    }

    /**
     * @return array
     * @throws IntegrityException
     */
    private function getConfig()
    {
        if (!$this->serviceUrl) {
            throw new IntegrityException('API endpoint was not specified');
        }

        return [
            'ServiceURL' => $this->serviceUrl,
            'ProxyHost' => $this->proxyHost,
            'ProxyPort' => $this->proxyPort ?: -1,
            'MaxErrorRetry' => $this->maxRetryCount,
        ];
    }

    /**
     * @return array
     */
    private function getMarketplaceIds()
    {
        return ['Id' => [$this->merchantId]];
    }
}