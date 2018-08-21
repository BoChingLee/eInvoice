<?php
/**
 * Created by PhpStorm.
 * User: ching
 * Date: 2018/8/21
 * Time: 5:14 PM
 */

namespace Ching\EInvoice\Exceptions;


class DonateCodeException extends \RuntimeException
{
    public static function getDetailFailed()
    {
        return new self("Donate Code is not found!");
    }
}