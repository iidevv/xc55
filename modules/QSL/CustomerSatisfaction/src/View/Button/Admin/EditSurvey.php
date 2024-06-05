<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\View\Button\Admin;

/**
 * Edit review popup button
 */
class EditSurvey extends \XLite\View\Button\Link
{
    /**
     * We make the full location path for the provided URL
     *
     * @return string
     */
    protected function getLocationURL()
    {
        return \XLite::getInstance()->getShopURL($this->getParam(static::PARAM_LOCATION));
    }

    /**
     * Return default button label
     *
     * @return string
     */
    protected function getDefaultLabel()
    {
        return '';
    }
}
