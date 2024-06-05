<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AMP\View;

use XCart\Extender\Mapping\ListChild;

/**
 * AMP side menu
 *
 * @ListChild (list="amp.body", weight="30")
 */
class Sidebar extends \XLite\View\AView implements \XLite\Core\PreloadedLabels\ProviderInterface
{
    /**
     * Array of labels in following format.
     *
     * 'label' => 'translation'
     *
     * @return mixed
     */
    public function getPreloadedLanguageLabels()
    {
        $list = [
            'Menu',
        ];

        $data = [];
        foreach ($list as $name) {
            $data[$name] = static::t($name);
        }

        return $data;
    }

    /**
     * @return bool
     */
    protected function isDisplayCategories()
    {
        $root = \XLite\Core\Database::getRepo('XLite\Model\Category')->getRootCategory();

        return $root && $root->hasSubcategories();
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate()
    {
        return 'modules/QSL/AMP/layout/sidebar.twig';
    }

    /**
     * Amp components
     *
     * @return array
     */
    protected function getAmpComponents()
    {
        return ['amp-sidebar'];
    }
}
