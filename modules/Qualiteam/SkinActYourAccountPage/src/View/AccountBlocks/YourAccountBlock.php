<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYourAccountPage\View\AccountBlocks;

/**
 *
 */
abstract class YourAccountBlock extends \XLite\View\AView
{
    public function getCSSFiles(): array
    {
        return array_merge(
            parent::getCSSFiles(),
            ['modules/Qualiteam/SkinActYourAccountPage/your_account_page.css'],
            ['modules/Qualiteam/SkinActYourAccountPage/messages_counter.css']
        );
    }

    protected function getDefaultTemplate(): string
    {
        return 'modules/Qualiteam/SkinActYourAccountPage/YourAccountBlock.twig';
    }

    /**
     * Check if widget is visible
     *
     * @return boolean
     */
    protected function isVisible(): bool
    {
        return parent::isVisible() && !empty($this->getBlockLinks());
    }

    /**
     * Get block title
     */
    abstract protected function getBlockTitle();

    /**
     * Get block image
     */
    abstract protected function getBlockImage();

    /**
     * Get block links and labels
     */
    abstract protected function getBlockLinks();
}