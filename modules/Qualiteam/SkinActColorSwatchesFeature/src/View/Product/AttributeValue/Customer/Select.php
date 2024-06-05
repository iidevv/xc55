<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActColorSwatchesFeature\View\Product\AttributeValue\Customer;

use Qualiteam\SkinActColorSwatchesFeature\Traits\ColorSwatchesTrait;
use XCart\Extender\Mapping\Extender;

/**
 * Attribute value (Select)
 * @Extender\Mixin
 * @Extender\After("QSL\ColorSwatches")
 */
class Select extends \XLite\View\Product\AttributeValue\Customer\Select
{
    use ColorSwatchesTrait;

    /**
     * Return widget template
     *
     * @return string
     */
    protected function getTemplate(): string
    {
        return (
            \XLite\Core\Layout::getInstance()->getZone() === \XLite::ZONE_CUSTOMER
            && $this->isColorSwatches()
            && $this->getAttrValue()
        )
            ? $this->getModulePath() . '/product/attribute_value/select/selectbox.twig'
            : parent::getTemplate();
    }

    /**
     * @param \XLite\Model\AttributeValue\AttributeValueSelect $value
     *
     * @return array
     */
    protected function getSwatchLinkAttributes(\XLite\Model\AttributeValue\AttributeValueSelect $value): array
    {
        $attributes = parent::getSwatchLinkAttributes($value);
        return array_merge($attributes, ['data-shipdate' => $value->getShipdate()]);
    }

    /**
     * Via this method the widget registers the JS files which it uses.
     * During the viewers initialization the JS files are collecting into the static storage.
     *
     * The method must return the array of the JS file paths:
     *
     * return array(
     *      'modules/Developer/Module/script.js',
     *      'script/js/main.js',
     * );
     *
     * Also the best practice is to use parent result:
     *
     * return array_merge(
     *      parent::getJSFiles(),
     *      array(
     *          'modules/Developer/Module/script.js',
     *          'script/js/main.js',
     *          ...
     *      )
     * );
     *
     * You shouldn't add the widget as a list child of 'body' because it won't have its JS resources loaded that way.
     * Use 'layout.main' or 'layout.footer' instead.
     *
     * @return array
     */
    public function getJSFiles(): array
    {
        $list = parent::getJSFiles();
        $list[] = $this->getModulePath() . '/product/input/controller.js';

        return $list;
    }

    /**
     * Via this method the widget registers the CSS files which it uses.
     * During the viewers initialization the CSS files are collecting into the static storage.
     *
     * The method must return the array of the CSS file paths:
     *
     * return array(
     *      'modules/Developer/Module/style.css',
     *      'styles/css/main.css',
     * );
     *
     * Also the best practice is to use parent result:
     *
     * return array_merge(
     *      parent::getCSSFiles(),
     *      array(
     *          'modules/Developer/Module/style.css',
     *          'styles/css/main.css',
     *          ...
     *      )
     * );
     *
     * LESS resource usage:
     * You can also use the less resources along with the CSS ones.
     * The LESS resources will be compiled into CSS.
     * However you can merge your LESS resource with another one using 'merge' parameter.
     * 'merge' parameter must contain the file path to the parent LESS file.
     * In this case the resources will be linked into one LESS file with the '@import' LESS instruction.
     *
     * !Important note!
     * Right now only one parent is supported, so you cannot link the resources in LESS chain.
     *
     * You shouldn't add the widget as a list child of 'body' because it won't have its CSS resources loaded that way.
     * Use 'layout.main' or 'layout.footer' instead.
     *
     * The best practice is to merge LESS resources with 'bootstrap/css/bootstrap.less' file
     *
     * @return array
     */
    public function getCSSFiles(): array
    {
        $list = parent::getCSSFiles();
        $list[] = $this->getModulePath() . '/product/input/style.less';

        return $list;
    }
}
