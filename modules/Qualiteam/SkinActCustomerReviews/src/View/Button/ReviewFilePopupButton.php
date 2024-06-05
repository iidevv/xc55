<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */


namespace Qualiteam\SkinActCustomerReviews\View\Button;


class ReviewFilePopupButton extends \XLite\View\Button\PopupButton
{
    protected function getButtonContent()
    {
        return $this->content;
    }

    protected function prepareURLParams()
    {
        return [
            'target' => 'review_file',
            'fileId' => $this->file->getId(),
            'isTmp' => $this->isTmp ?? false,
            'widget' => '\Qualiteam\SkinActCustomerReviews\View\ReviewFilePopupView',
        ];
    }

    protected function getDefaultTemplate()
    {
        return 'modules/Qualiteam/SkinActCustomerReviews/ReviewFilePopupButton.twig';
    }
}