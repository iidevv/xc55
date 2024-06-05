<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\View\Order\Search;

class Search extends \XLite\View\AView
{
    public static function getAllowedTargets()
    {
        $result   = parent::getAllowedTargets();
        $result[] = 'order_list';

        return $result;
    }

    /**
     * Return default template
     */
    protected function getDefaultTemplate(): string
    {
        return $this->getDir() . '/search.twig';
    }

    /**
     * Return templates directory name
     */
    protected function getDir(): string
    {
        return 'order';
    }

    /**
     * Check - search block visible or not
     */
    protected function isSearchVisible(): bool
    {
        return true;
    }

    /**
     * Define attributes
     */
    protected function getAttributes(): array
    {
        return [
           'data-widget' => 'XLite\View\Order\Search\Search'
        ];
    }
}
