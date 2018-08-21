<?php
/**
 * Created by PhpStorm.
 * User: ching
 * Date: 2018/8/21
 * Time: 5:00 PM
 */

namespace Ching\EInvoice;


use Ching\EInvoice\Exceptions\DonateCodeException;
use GuzzleHttp\Client;

class DonateCode
{
    /**
     * API version
     * @var string
     */
    const VERSION = "0.2";

    /**
     * API action
     * @var string
     */
    const ACTION = "qryLoveCode";

    /**
     * API url
     * @var string
     */
    const NORMAL_API = "https://api.einvoice.nat.gov.tw";

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
     * check Donate code is exists or not
     * @param $code
     * @param string $uuid
     * @return bool
     */
    public function exists($code, $uuid = "")
    {
        try {
            return (bool) $this->getDetail($code, $uuid);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * get donate code(love code) details
     * @param $code
     * @param string $uuid
     * @return mixed
     */
    public function getDetail($code, $uuid = "")
    {
        $response = $this->http->post(self::NORMAL_API . "/PB2CAPIVAN/loveCodeapp/qryLoveCode?" .
            $this->getUrlParameters($code, $uuid)
        );

        $content = $this->getDecodeContent($response->getBody()->getContents());

        if (! isset($content->details)) {
            throw DonateCodeException::getDetailFailed();
        }

        foreach ($content->details as $detail) {
            if (isset($detail->LoveCode) && $detail->LoveCode === $code) {
                return $detail;
            }
        }

        throw DonateCodeException::getDetailFailed();
    }

    /**
     * build http query
     * @param $code
     * @param $uuid
     * @return string
     */
    protected function getUrlParameters($code, $uuid)
    {
        return http_build_query([
            'version'   => self::VERSION,
            'qKey'      => $code,
            'action'    => self::ACTION,
            'UUID'      => $uuid ?: uniqid(),
            'appID'     => $this->getAppId()
        ]);
    }

    /**
     * Content 解碼
     * @param $content
     * @return mixed
     */
    protected function getDecodeContent($content)
    {
        return json_decode($content);
    }

}