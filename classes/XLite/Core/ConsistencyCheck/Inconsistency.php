<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\ConsistencyCheck;

/**
 * Class Inconsistency
 * @package XLite\Core\ConsistencyCheck
 */
class Inconsistency
{
    public const ERROR = 'error';
    public const WARNING = 'warning';
    public const NOTICE = 'notice';

    /**
     * @var string
     */
    protected $type;

    /**
     * @var string
     */
    protected $message;

    /**
     * Inconsistency constructor.
     *
     * @param string    $type
     * @param string    $message
     */
    public function __construct($type, $message)
    {
        $this->type = $type;
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
