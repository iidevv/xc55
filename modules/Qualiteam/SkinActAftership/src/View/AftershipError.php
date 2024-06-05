<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActAftership\View;

use Qualiteam\SkinActAftership\Traits\AftershipTrait;
use XLite\Core\Translation;
use XLite\Model\OrderTrackingNumber;
use XLite\Model\WidgetParam\TypeObject;
use XLite\View\AView;

class AftershipError extends AView
{
    use AftershipTrait;

    /** @var string */
    const PARAM_ITEM = 'item';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_ITEM => new TypeObject(
                'Order tracking number',
                null,
                false,
                OrderTrackingNumber::class
            ),
        ];
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();

        $list[] = $this->getModulePath() . '/order/aftership_error.less';

        return $list;
    }

    /**
     * @return OrderTrackingNumber|null
     */
    protected function getItem(): ?OrderTrackingNumber
    {
        return $this->getParam(self::PARAM_ITEM);
    }

    /**
     * @return bool|null
     */
    protected function hasError(): ?bool
    {
        return $this->getItem()->isAftershipSlugError();
    }

    /**
     * @return string
     */
    public function getErrorText(): string
    {
        return Translation::lbl('SkinActAftership carrier was not found for this tracking');
    }

    /**
     * Return widget default template
     *
     * @return string
     */
    protected function getDefaultTemplate(): string
    {
        return $this->getModulePath() . '/order/aftership_error.twig';
    }
}