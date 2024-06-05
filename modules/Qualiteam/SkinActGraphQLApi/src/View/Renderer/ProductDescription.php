<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActGraphQLApi\View\Renderer;

use Qualiteam\SkinActGraphQLApi\View\RendererAbstract;

/**
 * Mobile Admin settings dialog
 */
class ProductDescription extends RendererAbstract
{
    const PARAM_PRODUCT = 'product';

    /**
     * Return widget default template
     *
     * @return string
     */
    public function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActGraphQLApi/renderers/product_description.twig';
    }

    /**
     * @return array
     */
    protected function getCssStyles()
    {
        return array_merge(parent::getCssStyles(), [
            'froala-editor/css/froala_style.fixed.css',
            'modules/Qualiteam/SkinActGraphQLApi/renderers/user_content.css'
        ]);
    }

    /**
     * @return array
     */
    protected function getCacheParameters()
    {
        return array_merge(parent::getCacheParameters(), [
            'product' . $this->getProduct()->getId(),
        ]);
    }

    /**
     * Define widget parameters
     */
    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            self::PARAM_PRODUCT => new \XLite\Model\WidgetParam\TypeObject('Product', null),
        ];
    }

    /**
     * @return \XLite\Model\Product
     */
    protected function getProduct()
    {
        return $this->getParam(self::PARAM_PRODUCT);
    }
}
