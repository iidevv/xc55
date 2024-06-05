<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core;

/**
 * Widget data transport
 */
class WidgetDataTransport extends \XLite\Base
{
    /**
     * Handler to use
     *
     * @var \XLite\View\AView|null
     */
    protected $handler;


    /**
     * Save passed handler
     *
     * @param \XLite\View\AView|null $handler Passed handler
     *
     * @return void
     */
    public function __construct($handler)
    {
        $this->handler = $handler;
    }

    /**
     * Get widget
     *
     * @return \XLite\View\AView
     */
    public function getProtectedWidget()
    {
        return $this->handler;
    }

    /**
     * Call handler methods
     *
     * @param string $method Method to call
     * @param array  $args   Call arguments OPTIONAL
     *
     * @return mixed
     */
    public function __call($method, array $args = [])
    {
        return isset($this->handler) ? call_user_func_array([$this->handler, $method], $args) : null;
    }
}
