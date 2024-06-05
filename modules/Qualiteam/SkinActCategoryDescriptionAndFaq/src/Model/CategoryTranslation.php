<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCategoryDescriptionAndFaq\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * Category multilingual data
 * @Extender\Mixin
 */
class CategoryTranslation extends \XLite\Model\CategoryTranslation
{
    /**
     * Category bottom description
     *
     * @var string
     *
     * @ORM\Column (type="text")
     */
    protected $bottomDescription = '';

    /**
     * Set description
     *
     * @param string $description
     * @return \XLite\Model\CategoryTranslation
     */
    public function setBottomDescription($bottomDescription)
    {
        $this->bottomDescription = $bottomDescription;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getBottomDescription()
    {
        return $this->bottomDescription;
    }
}
