<?php

use Ching\EInvoice\DonateCode;
use PHPUnit\Framework\TestCase;

/**
 * Created by PhpStorm.
 * User: ching
 * Date: 2018/8/21
 * Time: 4:56 PM
 */


class DonateCodeTest extends TestCase
{
    public function testGetDetail()
    {
        $double = Mockery::mock(DonateCode::class);

        $double->shouldReceive('getDetail')
            ->with("1849")
            ->andReturn($this->fakeResponse());

        $result = $double->getDetail("1849");

        // excepts
        $excepts = $this->fakeResponse();

        $this->assertJsonStringEqualsJsonString(json_encode($result), json_encode($excepts));
    }

    /**
     * @return array
     */
    public function fakeResponse()
    {
        return [
            'rowNum' => 0,
            'SocialWelfareBAN'  => '18497679',
            'LoveCode' =>  "1849",
            'SocialWelfareName' => "財團法人法鼓山社會福利慈善事業基金會",
            'SocialWelfareAbbrev' =>  "法鼓山慈善"
        ];
    }
}
