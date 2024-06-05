<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class CleanURL extends \XLite\Model\CleanURL
{
    /**
     * Relation to a product entity
     *
     * @var \CDev\SimpleCMS\Model\Page
     *
     * @ORM\ManyToOne  (targetEntity="CDev\SimpleCMS\Model\Page", inversedBy="cleanURLs")
     * @ORM\JoinColumn (name="page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $page;

    /**
     * Set page
     *
     * @param \CDev\SimpleCMS\Model\Page $page
     * @return CleanURL
     */
    public function setPage(\CDev\SimpleCMS\Model\Page $page = null)
    {
        $this->page = $page;
        return $this;
    }

    /**
     * Get page
     *
     * @return \CDev\SimpleCMS\Model\Page
     */
    public function getPage()
    {
        return $this->page;
    }
}
