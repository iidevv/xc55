<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XCart\Extender\Mapping\ListChild;
use XLite\Model\OrderTrackingNumber;

/**
 * Show tracking button on customer zone
 *
 * @ListChild(list="orders.children.shipping.tracking", zone="customer", weight="100")
 * @ListChild(list="orders.children.parcels.part.tracking.number.after", zone="customer", weight="100")
 */
class OrderShippingTrackingButton extends \XLite\View\AView
{
    use AftershipTrait;

    const PARAM_ITEM = 'item';

    /**
     * Define widget parameters
     *
     * @return void
     */
    protected function defineWidgetParams(): void
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ITEM => new \XLite\Model\WidgetParam\TypeObject('Tracking number',
                null,
                false,
                OrderTrackingNumber::class
            ),
        ];
    }

    /**
     * Get tracking number item object
     *
     * @return object|null
     */
    public function getItem(): ?object
    {
        return $this->getParam(static::PARAM_ITEM);
    }

    /**
     * Default template
     */
    protected function getDefaultTemplate(): string
    {
        return $this->getModulePath() . '/items_list/order/parts/shipping.tracking.twig';
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

        $list[] = $this->getModulePath() . '/items_list/order/style.less';

        return $list;
    }
}