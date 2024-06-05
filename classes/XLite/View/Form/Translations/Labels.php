<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Form\Translations;

/**
 * Edit labels dialog form
 */
class Labels extends \XLite\View\Form\ItemsList\AItemsList
{
    public const DEFAULT_SECTION = 'design';

    /**
     * getDefaultTarget
     *
     * @return string
     */
    protected function getDefaultTarget()
    {
        return 'labels';
    }

    /**
     * Return list of the form default parameters
     *
     * @return array
     */
    protected function getDefaultParams()
    {
        $params = [
            'code'    => \XLite\Core\Request::getInstance()->code ?: static::getDefaultLanguage(),
            'section' => \XLite\Core\Request::getInstance()->section ?: self::DEFAULT_SECTION
        ];

        if (\XLite\Core\Request::getInstance()->label_id) {
            $params['label_id'] = \XLite\Core\Request::getInstance()->label_id;
        }

        return $params;
    }
}
