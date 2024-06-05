<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Model\Product;

use XCart\Extender\Mapping\Extender;
use Doctrine\ORM\Mapping as ORM;

/**
 * GlobalTab
 * @Extender\Mixin
 */
class GlobalTab extends \XLite\Model\Product\GlobalTab
{
    /**
     * Is tab available or not
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Link
     *
     * @var string
     *
     * @ORM\Column (type="string",nullable=true)
     */
    protected $link = null;

    /**
     * Global custom tab
     *
     * @var \XC\CustomProductTabs\Model\Product\CustomGlobalTab
     * @ORM\OneToOne  (targetEntity="XC\CustomProductTabs\Model\Product\CustomGlobalTab",
     *            mappedBy="global_tab", cascade={"all"})
     */
    protected $custom_tab;

    /**
     * Global custom tab
     *
     * @var \XC\CustomProductTabs\Model\Product\Tab[]
     * @ORM\OneToMany  (targetEntity="XC\CustomProductTabs\Model\Product\Tab", mappedBy="global_tab")
     */
    protected $product_specific_aliases;

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->product_specific_aliases = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Return Enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set Enabled
     *
     * @param boolean $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Return Link
     *
     * @return string
     */
    public function getLink()
    {
        return preg_replace('/[^a-z0-9-_]/i', '-', $this->link);
    }

    /**
     * Set Link
     *
     * @param string $link
     *
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Return CustomTab
     *
     * @return CustomGlobalTab
     */
    public function getCustomTab()
    {
        return $this->custom_tab;
    }

    /**
     * Set CustomTab
     *
     * @param CustomGlobalTab $custom_tab
     *
     * @return $this
     */
    public function setCustomTab($custom_tab)
    {
        $this->custom_tab = $custom_tab;
        return $this;
    }

    /**
     * Return ProductSpecificAliases
     *
     * @return \XC\CustomProductTabs\Model\Product\Tab[]
     */
    public function getProductSpecificAliases()
    {
        return $this->product_specific_aliases;
    }

    /**
     * Set ProductSpecificAliases
     *
     * @param \XC\CustomProductTabs\Model\Product\Tab $product_specific_alias
     *
     * @return $this
     */
    public function addProductSpecificAlias($product_specific_alias)
    {
        $this->product_specific_aliases[] = $product_specific_alias;
        return $this;
    }

    /**
     * Check if tab available
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->getEnabled() && parent::isAvailable();
    }

    /**
     * Returns tab name
     *
     * @return string
     */
    public function getName()
    {
        return $this->getCustomTab()
            ? $this->getCustomTab()->getName()
            : parent::getName();
    }
}
