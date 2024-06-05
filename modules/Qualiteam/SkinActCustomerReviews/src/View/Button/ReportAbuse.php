<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\View\Button;


use XLite\Core\Config;

class ReportAbuse extends \XLite\View\Button\Link
{

    protected function getReview()
    {
        return $this->review ?? null;
    }

    protected function isVisible()
    {
        return $this->getReview() && Config::getInstance()->XC->Reviews->display_report_abuse;
    }

    protected function getButtonLabel()
    {
        return static::t('SkinActCustomerReviews report abuse');
    }

    protected function getLocationURL()
    {
        $review = $this->getReview();

        if ($review) {
            return $this->buildURL('report_abuse', '', [
                'rid' => $review->getId(),
            ]);
        }

        return '';
    }

}