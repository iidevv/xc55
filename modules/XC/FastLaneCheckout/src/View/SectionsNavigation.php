<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\FastLaneCheckout\View;

use XC\FastLaneCheckout;

/**
 * Disable default one-page checkout in case of fastlane checkout
 */
class SectionsNavigation extends Sections
{
    /**
     * Register CSS files
     *
     * @return array
     */
    public function getCSSFiles()
    {
        return array_merge(
            parent::getCSSFiles(),
            [
                [
                    'file'  => FastLaneCheckout\Main::getSkinDir() . 'sections_navigation/style.less',
                    'media' => 'screen',
                    'merge' => 'bootstrap/css/bootstrap.less',
                ],
            ]
        );
    }

    public function getJSFiles()
    {
        return [
            FastLaneCheckout\Main::getSkinDir() . 'sections_navigation/navigation-item.js',
            FastLaneCheckout\Main::getSkinDir() . 'sections_navigation/navigation.js',
        ];
    }

    protected function getDefaultTemplate()
    {
        return FastLaneCheckout\Main::getSkinDir() . 'sections_navigation/template.twig';
    }

    /**
     * Defines the additional data array
     *
     * @return array
     */
    protected function defineWidgetData()
    {
        $widgetData = [
            'start_with' => null
        ];

        if ($this->hasUnfinishedAddress()) {
            $widgetData['start_with'] = 'address';
        }

        return $widgetData;
    }

    /**
     * Outputs the additional data as json text
     *
     * @return string
     */
    protected function getWidgetData()
    {
        return json_encode($this->defineWidgetData());
    }
}
