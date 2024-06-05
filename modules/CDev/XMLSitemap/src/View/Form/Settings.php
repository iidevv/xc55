<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\XMLSitemap\View\Form;

/**
 * Settings dialog model widget
 */
class Settings extends \XLite\View\Model\Settings
{
    /**
     * Get a list of CSS files required to display the widget properly
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                'modules/CDev/XMLSitemap/admin/settings.less'
            ]
        );
    }

    /**
     * Force change label for priority fields
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);
        if (
            $cell
            && isset($cell['min'], $cell['label'])
            && strpos($cell['class'], 'XLite\View\FormField\Input\Text\FloatInput') === 0
        ) {
            $cell['label'] = static::t('Priority');
        }

        return $cell;
    }

    /**
     * Return file name for body template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/CDev/XMLSitemap/admin/model/form/content.twig';
    }
}
