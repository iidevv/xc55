<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\ProductStickers\Controller;

use XCart\Extender\Mapping\Extender;
use XLite\Core\Config;

/**
 * @Extender\Mixin
 */
abstract class AController extends \XLite\Controller\AController
{
    /**
     * @return array
     */
    public function defineCommonJSData()
    {
        $list = parent::defineCommonJSData();

        $list['stickers'] = [
            'show_stickers_on_product_pages' => (bool) Config::getInstance()->QSL->ProductStickers->show_stickers_on_product_pages,
            'sticker_display_mode' => Config::getInstance()->QSL->ProductStickers->sticker_display_mode,
            'move_labels' => Config::getInstance()->QSL->ProductStickers->move_labels
        ];

        if (Config::getInstance()->QSL->ProductStickers->move_labels) {
            $list['stickers']['labels'] = $this->getLabelsData();
        }

        return $list;
    }

    /**
     * @return array
     */
    protected function getLabelsData()
    {
        return array_map(function ($label) {
            return [
                'name' => $this->getLabelClassByName($label->getName()),
                'bg_color' => $label->getBgColor(),
                'text_color' => $label->getTextColor()
            ];
        }, \XLite\Core\Database::getRepo('QSL\ProductStickers\Model\ProductSticker')->getLabels());
    }

    /**
     * @param $name
     *
     * @return string|null
     */
    protected function getLabelClassByName($name)
    {
        $data = [
            'Free shipping' => 'free-shipping',
            'Coming soon'   => 'coming-soon',
            'Sale'          => 'sale-price',
            'New!'          => 'new-arrival',
        ];

        return $data[$name] ?: null;
    }
}
