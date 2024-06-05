<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\CustomProductTabs\Model\Product;

use Doctrine\ORM\Mapping as ORM;
use XC\CustomProductTabs\Main;

/**
 * Custom global tab model class
 *
 * @ORM\Entity
 * @ORM\Table  (name="custom_global_tabs")
 *
 * @ORM\HasLifecycleCallbacks
 */
class CustomGlobalTab extends \XLite\Model\Base\I18n
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
     * Global tab
     *
     * @var \XLite\Model\Product\GlobalTab
     * @ORM\OneToOne  (targetEntity="XLite\Model\Product\GlobalTab", inversedBy="custom_tab")
     * @ORM\JoinColumn (name="global_tab_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $global_tab;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\CustomProductTabs\Model\Product\CustomGlobalTabTranslation", mappedBy="owner", cascade={"all"})
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
        $this->assignModule();
    }

    /**
     * Assign new link to tab if empty
     */
    public function assignLink()
    {
        if (
            $this->getGlobalTab()
            && !$this->getGlobalTab()->getLink()
        ) {
            $this->getGlobalTab()->setLink(
                \XLite\Core\Database::getRepo('\XLite\Model\Product\GlobalTab')->generateTabLink($this)
            );
        }
    }

    public function assignModule()
    {
        if (
            $this->getGlobalTab()
            && !$this->getGlobalTab()->getModule()
        ) {
            [$author, $name] = explode('-', Main::MODULE_ID);
            $this->getGlobalTab()->setModule("$author\\$name");
        }
    }

    /**
     * Create entity
     *
     * @return boolean
     */
    public function create()
    {
        if (!$this->getGlobalTab()) {
            $this->setGlobalTab(new \XLite\Model\Product\GlobalTab());
            $this->getGlobalTab()->setPosition(\XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->getMinPosition() - 10);
        }

        if (!$this->getGlobalTab()->isPersistent()) {
            \XLite\Core\Database::getEM()->persist($this->getGlobalTab());
            $createAliases = true;
        }

        $result = parent::create();

        if (isset($createAliases) && $result) {
            \XLite\Core\Database::getRepo('XLite\Model\Product\GlobalTab')->createGlobalTabAliases($this->getGlobalTab());
        }

        return $result;
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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return Enabled
     *
     * @return boolean
     */
    public function getEnabled()
    {
        return $this->getGlobalTab()->getEnabled();
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
        $this->getGlobalTab()->setEnabled($enabled);

        return $this;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getBriefInfo()
    {
        return $this->getTranslationField(__FUNCTION__);
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
     * @return string
     */
    public function getContent()
    {
        return $this->getTranslationField(__FUNCTION__);
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
