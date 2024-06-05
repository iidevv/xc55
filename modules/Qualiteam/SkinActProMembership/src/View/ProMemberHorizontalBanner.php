<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActProMembership\View;

use XLite\Model\WidgetParam\TypeBool;
use XLite\Model\WidgetParam\TypeString;

/**
 * ProMember dropdown widget
 */
class ProMemberHorizontalBanner extends \XLite\View\AView
{
    public const PARAM_CLASS_NAME = 'className';

    public const PARAM_COLOR = 'color';

    public const PARAM_SHOW_PRICE = 'showPrice';

    protected function defineWidgetParams()
    {
        parent::defineWidgetParams();

        $this->widgetParams += [
            static::PARAM_CLASS_NAME => new TypeString('Extra CSS classes', ''),
            static::PARAM_COLOR => new TypeString('Banner color', ''),
            static::PARAM_SHOW_PRICE => new TypeBool('Whether price is visible', false),
        ];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActProMembership/pro_membership_horizontal_banner.twig';
    }

    public function getCSSFiles()
    {
        $list = parent::getCSSFiles();
        $list[] = [
            'file'  => 'modules/Qualiteam/SkinActProMembership/css/less/pro_member_horizontal_banner.less',
            'media' => 'screen',
            'merge' => 'bootstrap/css/bootstrap.less',
        ];
        return $list;
    }

    protected function getClassName(): string
    {

        $classes = [
            'pro-membership-horizontal-banner',
        ];

        if ($extraClass = $this->getParam(static::PARAM_CLASS_NAME)) {
            $classes[] = $extraClass;
        }

        if ($color = $this->getParam(static::PARAM_COLOR)) {
            $classes[] = 'pro-membership-horizontal-banner--color--' . $color;
        }

        return implode(' ', $classes);
    }

    protected function priceIsVisible() {
        return $this->getParam(static::PARAM_SHOW_PRICE);
    }

    protected function getPriceLabel(): string
    {
        return static::t('SkinActProMembership PRO price');
    }

    protected function getOnClick()
    {
        static $result = null;

        if ($result === null) {
            $pid = (int)\XLite\Core\Config::getInstance()->Qualiteam->SkinActProMembership->product_to_add;

            if ($pid > 0) {
                $link = $this->buildURL('product', '', ['product_id' => $pid]);
                $result = 'onclick="window.location.href=\'' . $link . '\';"';
                return $result;
            }

            $result = '';
            return $result;
        }

        return $result;
    }

}