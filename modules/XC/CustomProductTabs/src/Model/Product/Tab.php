<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Model\Product;

use Doctrine\ORM\Mapping as ORM;

/**
 * The "tab" model class
 *
 * @ORM\Entity
 * @ORM\Table  (name="product_tabs",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="product_global_tab", columns={"product_id", "global_tab_id"})
 *      }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class Tab extends \XLite\Model\Base\I18n implements \XLite\Model\Product\IProductTab
{
    /**
     * Tab unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Tab position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

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
     * Tab product
     *
     * @var \XLite\Model\Product
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product", inversedBy="tabs")
     * @ORM\JoinColumn (name="product_id", referencedColumnName="product_id", onDelete="CASCADE")
     */
    protected $product;

    /**
     * Global tab product
     *
     * @var \XLite\Model\Product\GlobalTab
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Product\GlobalTab")
     * @ORM\JoinColumn (name="global_tab_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $global_tab;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\CustomProductTabs\Model\Product\TabTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Lifecycle callback
     *
     * @ORM\PrePersist
     */
    public function prepareBeforeSave()
    {
        $this->assignLink();
    }

    /**
     * Assign new link to tab if empty
     */
    public function assignLink()
    {
        if (
            !$this->isGlobal()
            && !$this->getLink()
        ) {
            $this->setLink(
                \XLite\Core\Database::getRepo('\XC\CustomProductTabs\Model\Product\Tab')
                    ->generateTabLink($this)
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function cloneEntity()
    {
        $new = parent::cloneEntity();

        $new->setGlobalTab($this->getGlobalTab());

        return $new;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set position
     *
     * @param integer $position
     *
     * @return $this
     */
    public function setPosition($position)
    {
        $this->position = $position;
        return $this;
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set enabled
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
     * Get enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Return Link
     *
     * @return mixed
     */
    public function getLink()
    {
        return preg_replace('/[^a-z0-9-_]/i', '-', $this->link);
    }

    /**
     * Set Link
     *
     * @param mixed $link
     *
     * @return $this
     */
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }

    /**
     * Set product
     *
     * @param \XLite\Model\Product $product
     *
     * @return $this
     */
    public function setProduct(\XLite\Model\Product $product = null)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Get product
     *
     * @return \XLite\Model\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Return GlobalTab
     *
     * @return \XLite\Model\Product\GlobalTab
     */
    public function getGlobalTab()
    {
        return $this->global_tab;
    }

    /**
     * Set GlobalTab
     *
     * @param \XLite\Model\Product\GlobalTab $global_tab
     *
     * @return $this
     */
    public function setGlobalTab($global_tab)
    {
        $this->global_tab = $global_tab;
        return $this;
    }

    /**
     * Return Name
     *
     * @return string|null
     */
    public function getServiceName()
    {
        return $this->getGlobalTab()
            ? $this->getGlobalTab()->getServiceName()
            : null;
    }


    /**
     * Check if tab available
     *
     * @return bool
     */
    public function isAvailable()
    {
        $result = $this->isGlobalStatic()
            ? $this->getGlobalTab()->checkProviders()
            : true;

        return $result && $this->getEnabled();
    }

    /**
     * Check if tab is alias to global
     *
     * @return bool
     */
    public function isGlobal()
    {
        return (bool)$this->getGlobalTab();
    }

    /**
     * Check if tab is alias to global custom
     *
     * @return bool
     */
    public function isGlobalCustom()
    {
        return $this->isGlobal() && $this->getGlobalTab()->getCustomTab();
    }


    /**
     * Check if tab is alias to global static
     *
     * @return bool
     */
    public function isGlobalStatic()
    {
        return $this->getGlobalTab() && $this->getGlobalTab()->getServiceName();
    }

    // {{{ Translation Getters / setters

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->isGlobal()
            ? $this->getGlobalTab()->getName()
            : $this->getTranslationField(__FUNCTION__);
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getBriefInfo()
    {
        return $this->isGlobalCustom()
            ? $this->getGlobalTab()->getCustomTab()->getBriefInfo()
            : $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $brief_info
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setBriefInfo($brief_info)
    {
        return $this->setTranslationField(__FUNCTION__, $brief_info);
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->isGlobalCustom()
            ? $this->getGlobalTab()->getCustomTab()->getContent()
            : $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $content
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setContent($content)
    {
        return $this->setTranslationField(__FUNCTION__, $content);
    }

    // }}}
}
