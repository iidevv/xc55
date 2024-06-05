<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActFrequentlyBoughtTogether\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Product extends \XLite\Model\Product
{
    /**
     * Is product exclude from frequently bought together or not
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean", nullable=true)
     */
    protected $isExcludeFreqBoughtTogether = false;

    /**
     * @param bool|null $value
     *
     * @return void
     */
    public function setExcludeFreqBoughtTogether(bool $value): void
    {
        $this->isExcludeFreqBoughtTogether = $value;
    }

    /**
     * @return bool|null
     */
    public function getExcludeFreqBoughtTogether(): ?bool
    {
        return $this->isExcludeFreqBoughtTogether;
    }

    protected function showPlaceholderOption()
    {
        if (\XLite\Core\Request::getInstance()->freq_bought_together_mode) {
            return false;
        }

        return parent::showPlaceholderOption();
    }
}