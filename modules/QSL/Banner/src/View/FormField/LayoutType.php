<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\View\FormField;

class LayoutType extends \XLite\View\FormField\Label
{
    /**
     * @return array
     */
    public function getCSSFiles()
    {
        $list   = parent::getCSSFiles();
        $list[] = 'modules/QSL/Banner/form_field/layout_type.less';

        return $list;
    }

    /**
     * @return array
     */
    protected function getBannerLocation()
    {
        return $this->entity->location;
    }

    /**
     * @return string
     */
    protected function getFieldTemplate()
    {
        return 'modules/QSL/Banner/form_field/banner_location.twig';
    }

    /**
     * @return string
     */
    protected function getDir()
    {
        return '';
    }

    /**
     * Returns layout type image
     *
     * @param string $value Layout type
     *
     * @return string
     */
    protected function getImage($value)
    {
        return $this->getSVGImage('modules/QSL/Banner/images/layout/' . $value . '.svg');
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible()
    {
        return parent::isVisible();
    }
}
