<?php
/**
 * Created by PhpStorm.
 * User: ching
 * Date: 2018/8/21
 * Time: 12:06 PM
 */


use Ching\EInvoice\BarcodeVerified;
use PHPUnit\Framework\TestCase;

class BarcodeVerifiedTest extends TestCase
{
    public $config = [
        'appId' => '12345'
    ];

    public function setUp()
    {
    }

    public function tearDown()
    {

    }

    /**
     * @dataProvider verifiedProvider
     * @param $barcode string 手機載具
     * @param $txId string 營業人流水號
     * @param $expect string 期望結果
     * @param bool $equal 是否正確
     */
    public function testVerified($barcode, $txId, $expect, $equal = true)
    {
        $double = Mockery::mock(BarcodeVerified::class);

        $double->shouldReceive("verified")
            ->with($barcode, $txId)
            ->andReturn($this->fakeResponse(true, $txId));

        $response = $double->verified($barcode, $txId);

        if ($equal)
            $this->assertJsonStringEqualsJsonString($response, $expect);
        else
            $this->assertJsonStringNotEqualsJsonString($response, $expect);
    }

    /**
     * @dataProvider verifiedProvider
     * @param $barcode string 手機載具
     * @param $txId string 營業人流水號
     * @param $expect string 期望結果
     * @param bool $equal 是否正確
     */
    public function testExists($barcode, $txId, $expect, $equal = true)
    {
        $double = Mockery::mock(BarcodeVerified::class);

        $result = json_decode($expect);

        $double->shouldReceive("exists")
            ->with($barcode, $txId)
            ->andReturn($result->isExist === "Y");

        $response = $double->exists($barcode, $txId);

        if ($equal)
            $this->assertTrue($response);
        else
            $this->assertFalse($response);

    }

    /**
     * 驗證資料 provider
     * @return array
     */
    public function verifiedProvider()
    {
        return [
            ["/-MAAAAA" , "0001", $this->fakeResponse(), true],
            ["/-MBBBBB" , "0002", $this->fakeResponse(false, "0002"), false],
            ["/-MCCCCC" , "0003", $this->fakeResponse(false), false]
        ];
    }

    /**
     * 假資料產生
     * @param bool $exists
     * @param string $txId
     * @param int $code
     * @return string
     */
    protected function fakeResponse($exists = true, $txId = "0001", $code = 200)
    {
        return json_encode([
            'isExist' => $exists ? 'Y' : 'N',
            'code'    => $code,
            'msg'     => '執行成功',
            'version' => '0.1',
            'TxID'    => $txId
        ]);
    }
}
