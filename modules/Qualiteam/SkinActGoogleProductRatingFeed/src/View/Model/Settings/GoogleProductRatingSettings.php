<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGoogleProductRatingFeed\View\Model\Settings;

use Qualiteam\SkinActGoogleProductRatingFeed\Main;
use Qualiteam\SkinActGoogleProductRatingFeed\Traits\SkinActGoogleProductRatingFeedTrait;
use XLite\View\FormField\AFormField;

class GoogleProductRatingSettings extends \XLite\View\Model\Settings
{
    use SkinActGoogleProductRatingFeedTrait;

    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/settings.less';

        return $list;
    }

    protected function prepareDataForMapping()
    {
        $data = parent::prepareDataForMapping();

        foreach ($data as $name => $value) {
            if (substr($name, -6) === '_field') {
                $data[$name] = serialize($value);
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

    protected function getFormFieldByOption(\XLite\Model\Config $option)
    {
        $cell = parent::getFormFieldByOption($option);

        if ($cell['label'] === 'Product feed key') {
            $cell[AFormField::PARAM_COMMENT] = Main::getGoogleProductRatingFeedUrl() ?
                static::t('SkinActGoogleProductRatingFeed rating feed is available by the URL: X', [
                'url' => Main::getGoogleProductRatingFeedUrl(),
            ]) : static::t('SkinActGoogleProductRatingFeed google product rating feed has not been generated yet');
        }

        return $cell;
    }
}