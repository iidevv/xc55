<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\View;


use XLite\Core\Config;

class UsefulView extends \XLite\View\AView
{
    protected function isVisible()
    {
        return parent::isVisible() && Config::getInstance()->XC->Reviews->display_useful_to_you;
    }

    public function getJSFiles()
    {
        $list = parent::getJSFiles();
        $list[] = 'modules/Qualiteam/SkinActCustomerReviews/UsefulView.js';
        return $list;
    }

    protected function getUsefulYes()
    {
        $count = $this->review->getUseful();
        $id = $this->review->getId();

        $button = new \XLite\View\Button\Link([
            \XLite\View\Button\AButton::PARAM_LABEL => static::t('SkinActCustomerReviews useful Yes') . ' (' . $count . ')',
            \XLite\View\Button\Link::PARAM_JS_CODE => "voteUseful($id, 1);",
            \XLite\View\Button\AButton::PARAM_ATTRIBUTES => [
                'data-id' => $id,
                'data-type' => 'useful'
            ]

        ]);

        return $button->getContent();
    }

    protected function getUsefulNo()
    {
        $count = $this->review->getNonUseful();
        $id = $this->review->getId();

        $button = new \XLite\View\Button\Link([
            \XLite\View\Button\AButton::PARAM_LABEL => static::t('SkinActCustomerReviews useful No') . ' (' . $count . ')',
            \XLite\View\Button\Link::PARAM_JS_CODE => "voteUseful($id, 0);",
            \XLite\View\Button\AButton::PARAM_ATTRIBUTES => [
                'data-id' => $id,
                'data-type' => 'nonuseful'
            ]
        ]);

        return $button->getContent();
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCustomerReviews/UsefulView.twig';
    }
}