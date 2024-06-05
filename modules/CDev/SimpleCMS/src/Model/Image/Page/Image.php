<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\SimpleCMS\Model\Image\Page;

use Doctrine\ORM\Mapping as ORM;

/**
 * Page image
 *
 * @ORM\Entity
 * @ORM\Table  (name="page_images")
 */
class Image extends \XLite\Model\Base\Image
{
    /**
     * Relation to a page entity
     *
     * @var \CDev\SimpleCMS\Model\Page
     *
     * @ORM\OneToOne   (targetEntity="CDev\SimpleCMS\Model\Page", inversedBy="image")
     * @ORM\JoinColumn (name="page_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $page;

    /**
     * Set page
     *
     * @param \CDev\SimpleCMS\Model\Page $page
     * @return Image
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
