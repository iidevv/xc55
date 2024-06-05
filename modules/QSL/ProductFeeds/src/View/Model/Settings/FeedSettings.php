<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductFeeds\View\Model\Settings;

use XLite\View\FormField\AFormField;

/**
 * Feed settings form.
 */
class FeedSettings extends \XLite\View\Model\Settings
{
    /**
     * Register CSS files.
     *
     * @return array
     */
    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = 'modules/QSL/ProductFeeds/feed_settings/feed-settings.css';

        return $list;
    }

    protected function prepareDataForMapping()
    {
        $data = parent::prepareDataForMapping();

        foreach ($data as $name => $value) {
            if (substr($name, -6) === '_field') {
                $data[$name] = serialize($value);
            } elseif ($name === 'googleshop_taxonomy_version') {
                // Do not update the taxonomy date
                unset($data[$name]);
            }
        }

        return $data;
    }

    /**
     * Retrieve property from the model object
     *
     * @param mixed $name Field/property name
     *
     * @return mixed
     */
    protected function getModelObjectValue($name)
    {
        $value = parent::getModelObjectValue($name);

        if (substr($name, -6) === '_field') {
            $v = @unserialize($value);
            if ($v || (serialize(false) == $value)) {
                $value = $v;
            } else {
                $value = [];
            }
        }

        return $value;
    }

    /**
     * Get form field by option
     *
     * @param \XLite\Model\Config $option Option
     *
     * @return array
     */
    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $field = parent::getFormFieldByOption($option);

        // Tricky hack to get the option_comment for the "setting" displayed near the option name, not the option value
        if ($this->isHelpLabelForTitle($option->getName()) && isset($field[AFormField::PARAM_HELP])) {
            $field[self::SCHEMA_HELP] = $field[AFormField::PARAM_HELP];
            unset($field[AFormField::PARAM_HELP]);
        }

        return $field;
    }

    /**
     * Check if it is a setting that the help label should be displayed next to the setting name for.
     *
     * @param string $optionName Setting name.
     *
     * @return boolean
     */
    protected function isHelpLabelForTitle($optionName)
    {
        return in_array(
            $optionName,
            [
                'googleshop_taxonomy_version',
                'googleshop_price_measure_field',
                'googleshop_pbase_measure_field',
            ]
        );
    }

    /**
     * Return list of the "Button" widgets
     *
     * @return array
     */
    protected function getFormButtons()
    {
        $result = parent::getFormButtons();

        $result['product-feeds-list'] = $this->getWidget(
            [
                \XLite\View\Button\AButton::PARAM_LABEL => static::t('Back to feeds list'),
                \XLite\View\Button\AButton::PARAM_STYLE => 'action product-feeds-list-back-button',
                \XLite\View\Button\Link::PARAM_LOCATION => $this->buildURL('product_feeds'),
            ],
            '\XLite\View\Button\SimpleLink'
        );

        return $result;
    }
}
