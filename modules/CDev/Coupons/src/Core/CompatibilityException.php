<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Coupons\Core;

/**
 * Coupon compatibility exception
 */
class CompatibilityException extends \XLite\Core\Exception
{
    /**
     * Coupon
     *
     * @var \CDev\Coupons\Model\Coupon
     */
    protected $coupon;

    /**
     * Message params
     *
     * @var array
     */
    protected $params;

    /**
     * Error code
     *
     * @var string
     */
    protected $errorCode;

    /**
     * Constructor
     *
     * @param string                                  $message  Message text
     * @param array                                   $params   Message params
     * @param \CDev\Coupons\Model\Coupon $coupon   Coupon
     * @param string                                  $code     Code
     * @param \Exception                              $previous Previous
     */
    public function __construct(
        $message = '',
        array $params = [],
        \CDev\Coupons\Model\Coupon $coupon = null,
        $code = '',
        \Exception $previous = null
    ) {
        parent::__construct($message, 0, $previous);

        $this->setErrorCode($code);
        $this->setParams($params);
        $this->setCoupon($coupon);
    }

    /**
     * Set coupon
     *
     * @param \CDev\Coupons\Model\Coupon $coupon Coupon
     *
     * @return void
     */
    public function setCoupon(\CDev\Coupons\Model\Coupon $coupon)
    {
        $this->coupon = $coupon;
    }

    /**
     * Returns coupon
     *
     * @return \CDev\Coupons\Model\Coupon
     */
    public function getCoupon()
    {
        return $this->coupon;
    }

    /**
     * Set params
     *
     * @param array $params Params OPTIONAL
     *
     * @return void
     */
    public function setParams(array $params = [])
    {
        $this->params = $params;
    }

    /**
     * Returns params
     *
     * @return array
     */
    public function getParams()
    {
        return $this->params ?: [];
    }

    /**
     * Set error code
     *
     * @param string $errorCode Error code OPTIONAL
     *
     * @return void
     */
    public function setErrorCode($errorCode = '')
    {
        $this->errorCode = $errorCode;
    }

    /**
     * Returns error code
     *
     * @return array
     */
    public function getErrorCode()
    {
        return $this->errorCode ?: '';
    }
}
