<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActPinterestPixel\View;

use Qualiteam\SkinActPinterestPixel\Main;
use XCart\Extender\Mapping\ListChild;
use XLite\Core\Auth;

/**
 * @ListChild(list="head", zone="customer", weight="1500")
 */
class PinterestPixel extends \XLite\View\AView
{
    protected function getDefaultTemplate()
    {
        return Main::getModulePath() . '/body.twig';
    }

    public function isVisible()
    {
        return parent::isVisible()
            && Main::isPixelEnabled();
    }

    public function isUserHasEmail()
    {
        return Auth::getInstance()->getProfile()
            && Auth::getInstance()->getProfile()->getEmail();
    }

    public function getPixelAdvancedParams()
    {
        $params = [];

        if ($this->isUserHasEmail()) {
            $params['em'] = Auth::getInstance()->getProfile()->getEmail();
        }

        return $params ? json_encode($params) : false;
    }

    public function getPixelSrc()
    {
        $url = Main::getPixelUrl();

        $params = [
            'tid' => $this->getPixelTagId(),
            'event' => 'init',
            'noscript' => 1,
        ];

        if ($this->isUserHasEmail()) {
            $params['pd']['em'] = sha1(Auth::getInstance()->getProfile()->getEmail());
        }

        return $url . "?" . urldecode(http_build_query($params, '', '&'));
    }

    public function getPixelTagId()
    {
        return \XLite\Core\Config::getInstance()->Qualiteam->SkinActPinterestPixel->script_code;
    }
}