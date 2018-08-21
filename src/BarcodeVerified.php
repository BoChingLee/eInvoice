<?php
/**
 * Created by PhpStorm.
 * User: ching
 * Date: 2018/8/21
 * Time: 3:50 PM
 */

namespace Ching\EInvoice;


use Ching\EInvoice\Exceptions\BarcodeException;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;

class BarcodeVerified
{
    /**
     * API version
     * @var string
     */
    const VERSION = "1.0";

    /**
     * API action
     * @var string
     */
    const ACTION = "bcv";

    /**
     * API url
     * @var string
     */
    const API_URL = "http://www-vc.einvoice.nat.gov.tw/BIZAPIVAN/biz";

    /**
     * @var Client
     */
    protected $http;

    /**
     * @var string
     */
    protected $appId;

    /**
     * BarcodeVerified constructor.
     * @param array $config
     * @param Client|null $client
     */
    public function __construct(array $config, Client $client = null)
    {
        $this->http = $client ?: new Client();

        $this->setAppId(isset($config['appId']) ? $config['appId'] : null);
    }

    /**
     * 手機條碼是否存在
     * @param $barcode string 手機條碼
     * @param string $txID 營業人自行提供系統序號(或流水號)
     * @return string
     */
    public function exists($barcode, $txID)
    {
        $result = json_decode($this->verified($barcode, $txID));

        return $result->isExist === "Y";
    }

    /**
     * 手機條碼驗證
     * @param $barcode string 手機條碼
     * @param string $txID 營業人自行提供系統序號(或流水號)
     * @return string
     */
    public function verified($barcode, $txID = "")
    {
        $response = $this->http->post(self::API_URL . "?" . $this->getUrlParameters($barcode, $txID));

        return $this->verifyCode($response->getBody()->getContents());
    }

    /**
     * @param $barcode
     * @param $txID
     * @return string
     */
    private function getUrlParameters($barcode, $txID)
    {
        return http_build_query([
            "version"   => self::VERSION,
            "action"    => self::ACTION,
            "barCode"   => $barcode,
            "TxID"      => $txID ?: "0001",
            "appId"     => $this->getAppId()
        ]);
    }

    /**
     * setup app id
     * @param string|null $appId
     * @return $this
     */
    public function setAppId($appId = null)
    {
        $this->appId = $appId;

        return $this;
    }

    /**
     * get api id
     * @return string
     */
    private function getAppId()
    {
        if (! $this->appId) {
            throw BarcodeException::appIdNotDefined();
        }

        return $this->appId;
    }

    /**
     * @param $result
     * @return bool
     */
    private function verifyCode($result)
    {
        $body = json_decode($result);

        return $body->code !== 200;
    }
}