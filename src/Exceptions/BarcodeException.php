<?php
/**
 * Created by PhpStorm.
 * User: ching
 * Date: 2018/8/21
 * Time: 12:37 PM
 */

namespace Ching\EInvoice\Exceptions;


class BarcodeException extends \RuntimeException
{
    /**
     * App not defined
     * @return BarcodeException
     */
    public static function appIdNotDefined()
    {
        return new self("App ID is not defined!");
    }
}