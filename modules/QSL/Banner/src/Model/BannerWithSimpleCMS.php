<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\Banner\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\SimpleCMS")
 */
class BannerWithSimpleCMS extends \QSL\Banner\Model\Banner
{
    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="CDev\SimpleCMS\Model\Page", inversedBy="banners")
     * @ORM\JoinTable (name="banner_page",
     *      joinColumns={@ORM\JoinColumn (name="banner_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="id", referencedColumnName="id", onDelete="CASCADE")}
     * )
     */
    protected $pages;

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->pages = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Add pages
     *
     * @param \CDev\SimpleCMS\Model\Page $pages
     * @return Banner
     */
    public function addPages(\CDev\SimpleCMS\Model\Page $pages)
    {
        $this->pages[] = $pages;
        return $this;
    }

    /**
     * Get memberships
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * Clear memberships
     */
    public function clearPages()
    {
        foreach ($this->getPages()->getKeys() as $key) {
            $this->getPages()->remove($key);
        }
    }
}
