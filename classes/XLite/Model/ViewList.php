<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Includes\Utils\Module\Manager;
use Includes\Utils\Module\Module;

/**
 * View list
 *
 * @ORM\Entity
 * @ORM\Table  (name="view_lists",
 *          indexes={
 *              @ORM\Index (name="forGenerateOneBannerPerViewList", columns={"entityId"}),
 *              @ORM\Index (name="tl", columns={"tpl", "list"}),
 *              @ORM\Index (name="lzv", columns={"list", "zone", "version"}),
 *              @ORM\Index (name="tclz", columns={"tpl", "child", "list", "zone"})
 *          }
 * )
 * @ORM\HasLifecycleCallbacks
 */
class ViewList extends \XLite\Model\AEntity
{
    /**
     * Predefined weights
     */
    public const POSITION_FIRST = 0;
    public const POSITION_LAST  = 16777215;

    /** @deprecated */
    public const INTERFACE_CUSTOMER = 'customer';
    /** @deprecated */
    public const INTERFACE_ADMIN    = 'admin';

    /**
     * Predefined interfaces
     */
    public const INTERFACE_WEB  = 'web';
    public const INTERFACE_MAIL = 'mail';
    public const INTERFACE_PDF  = 'pdf';

    /**
     * Predefined zones
     */
    public const ZONE_CUSTOMER = 'customer';
    public const ZONE_ADMIN    = 'admin';

    /**
     * Override modes
     */
    public const OVERRIDE_OFF = 0;
    public const OVERRIDE_MOVE = 1;
    public const OVERRIDE_HIDE = 2;
    public const OVERRIDE_DISABLE_PRESET = 3;

    /**
     * Layout preset key
     */
    public const PRESET_ONE_COLUMN = 'one';

    /**
     * Version key
     *
     * @var string
     */
    protected static $versionKey;

    /**
     * List id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", length=11)
     */
    protected $list_id;

    /**
     * Parent view list item
     *
     * @var \XLite\Model\ViewList
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\ViewList", inversedBy="variants")
     * @ORM\JoinColumn (name="parent_id", referencedColumnName="list_id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * Variants of view list item
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\ViewList", mappedBy="parent")
     * @ORM\OrderBy ({"list_id"="ASC"})
     */
    protected $variants;

    /**
     * Class list name
     *
     * @var string
     *
     * @ORM\Column (type="string", options={"charset"="latin1"})
     */
    protected $list;

    /**
     * List interface
     *
     * @var string
     *
     * @ORM\Column (type="string", length=16, options={"charset"="latin1"})
     */
    protected $interface = self::INTERFACE_WEB;

    /**
     * List zone
     *
     * @var string
     *
     * @ORM\Column (type="string", length=16, options={"charset"="latin1"})
     */
    protected $zone = self::ZONE_CUSTOMER;

    /**
     * Child class name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=512, options={"charset"="latin1"})
     */
    protected $child = '';

    /**
     * Child weight
     *
     * @var integer
     *
     * @ORM\Column (type="integer", length=11)
     */
    protected $weight = 0;

    /**
     * Template relative path
     *
     * @var string
     *
     * @ORM\Column (type="string", length=512, options={"charset"="latin1"})
     */
    protected $tpl = '';

    /**
     * Template relative path
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32, nullable=true)
     */
    protected $version;

    /**
     * Template relative path
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32, nullable=true)
     */
    protected $preset;

    /**
     * Class list name
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $list_override = '';

    /**
     * Child weight
     *
     * @var integer
     *
     * @ORM\Column (type="integer", length=11)
     */
    protected $weight_override = 0;

    /**
     * Override mode
     *
     * @var boolean
     *
     * @ORM\Column (type="integer")
     */
    protected $override_mode = 0;

    /**
     * Is class or template is deleted
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $deleted = false;

    /**
     * Contains additional conditions
     *
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true)
     */
    protected $entityId;

    public function __construct(array $data = [])
    {
        $this->variants = new ArrayCollection();
        parent::__construct($data);
    }

    /**
     * Get in zone hash
     *
     * @return string
     */
    public function getHashWithoutZone()
    {
        $prefix = \XLite::ZONE_COMMON . '/';
        $pattern = '/^' . preg_quote($prefix, '/') . '/uS';

        $hashValues = [
            $this->getList(),
            $this->getChild(),
            $this->getWeight(),
            preg_replace($pattern, '', $this->getTpl()),
        ];

        return md5(serialize($hashValues));
    }

    /**
     * Set version key
     *
     * @param string $key Key
     *
     * @return void
     */
    public static function setVersionKey($key)
    {
        static::$versionKey = $key;
    }

    /**
     * Prepare creation date
     *
     * @return void
     *
     * @ORM\PrePersist
     */
    public function prepareBeforeCreate()
    {
        if (static::$versionKey && !$this->getVersion()) {
            $this->setVersion(static::$versionKey);
        }
    }

    /**
     * Prepare remove
     *
     * @return void
     *
     * @ORM\PreRemove
     */
    public function prepareBeforeRemove()
    {
        if ($this->parent) {
            $this->parent->removeVariant($this);
        }

        foreach ($this->variants->getIterator() as $child) {
            $child->deleteParent();
        }
    }

    /**
     * Get list_id
     *
     * @return integer
     */
    public function getListId()
    {
        return $this->list_id;
    }

    /**
     * Set parent view list item
     *
     * @param ViewList $parent
     *
     * @return ViewList
     */
    public function setParent(ViewList $parent)
    {
        $parent->addVariant($this);
        $this->parent = $parent;
        return $this;
    }

    /**
     * Get parent view list
     *
     * @return ViewList
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Delete parent
     *
     * @return $this
     */
    public function deleteParent()
    {
        $this->parent = null;
        return $this;
    }

    /**
     * Set list
     *
     * @param string $list
     * @return ViewList
     */
    public function setList($list)
    {
        $this->list = $list;
        return $this;
    }

    /**
     * Get list
     *
     * @return string
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * Set interface
     *
     * @param string $interface
     *
     * @return ViewList
     */
    public function setInterface($interface)
    {
        $this->interface = $interface;
        return $this;
    }

    /**
     * Get zone
     *
     * @return string
     */
    public function getInterface()
    {
        return $this->interface;
    }

    /**
     * Set zone
     *
     * @param string $zone
     * @return ViewList
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
        return $this;
    }

    /**
     * Get zone
     *
     * @return string
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Set child
     *
     * @param string $child
     * @return ViewList
     */
    public function setChild($child)
    {
        $this->child = $child;
        return $this;
    }

    /**
     * Get child
     *
     * @return string
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Set weight
     *
     * @param integer $weight
     * @return ViewList
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
        return $this;
    }

    /**
     * Get weight
     *
     * @return integer
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * Set tpl
     *
     * @param string $tpl
     * @return ViewList
     */
    public function setTpl($tpl)
    {
        $this->tpl = $tpl;
        return $this;
    }

    /**
     * Get tpl
     *
     * @return string
     */
    public function getTpl()
    {
        return $this->tpl;
    }

    /**
     * Set version
     *
     * @param string $version
     * @return ViewList
     */
    public function setVersion($version)
    {
        $this->version = $version;
        return $this;
    }

    /**
     * Get version
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set list_override
     *
     * @param string $listOverride
     * @return ViewList
     */
    public function setListOverride($listOverride)
    {
        $this->list_override = $listOverride;
        return $this;
    }

    /**
     * Get list_override
     *
     * @return string
     */
    public function getListOverride()
    {
        return $this->list_override;
    }

    /**
     * Set weight_override
     *
     * @param integer $weightOverride
     * @return ViewList
     */
    public function setWeightOverride($weightOverride)
    {
        $this->weight_override = $weightOverride;
        return $this;
    }

    /**
     * Get weight_override
     *
     * @return integer
     */
    public function getWeightOverride()
    {
        return $this->weight_override;
    }

    /**
     * Set preset
     *
     * @param string $preset
     * @return ViewList
     */
    public function setPreset($preset)
    {
        $this->preset = $preset;
        return $this;
    }

    /**
     * Get preset
     *
     * @return string
     */
    public function getPreset()
    {
        return $this->preset;
    }

    /**
     * @return bool
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param bool $deleted
     * @return ViewList
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
        return $this;
    }

    /**
     * @param integer $entityId
     *
     * @return ViewList
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;
        return $this;
    }

    /**
     * @return integer
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Get view list variants
     *
     * @return ArrayCollection
     */
    public function getVariants()
    {
        return $this->variants;
    }

    /**
     * Add view list variant
     *
     * @param ViewList $entity
     * @return $this
     */
    public function addVariant(ViewList $entity)
    {
        $this->variants[] = $entity;

        return $this;
    }

    /**
     * Remove view list variant
     *
     * @param ViewList $entity
     * @return bool
     */
    public function removeVariant(ViewList $entity)
    {
        return $this->variants->removeElement($entity);
    }

    /**
     * Returns name of view list where this item will be actually displayed (takes overrides into account)
     *
     * @return string
     */
    public function getListActual()
    {
        if ($this->isDisplayed()) {
            if ($this->getOverrideMode() > static::OVERRIDE_OFF) {
                return $this->getListOverride();
            }
            return $this->getList();
        }
        return 'hidden';
    }

    /**
     * Returns view list item weight considering overrides
     *
     * @return integer
     */
    public function getWeightActual()
    {
        return $this->getOverrideMode() ? $this->getWeightOverride() : $this->getWeight();
    }

    /**
     * Check if this view list item will be rendered
     *
     * @return boolean
     */
    public function isDisplayed()
    {
        return !$this->isHidden();
    }

    /**
     * Check if this view list item is in hidden mode (not rendered in customer area and rendered invisible in layout editor)
     * @return boolean
     */
    public function isHidden()
    {
        return $this->getOverrideMode() === static::OVERRIDE_HIDE;
    }

    /**
     * Apply override settings
     *
     * @param integer $mode
     * @param string $list
     * @param integer $weight
     */
    public function applyOverrides($mode, $list = null, $weight = null)
    {
        $this->setOverrideMode($mode);

        if ($list !== null) {
            $this->setListOverride($list);
        }

        if ($weight !== null) {
            $this->setWeightOverride($weight);
        }
    }

    /**
     * Transfer override settings from another view list item
     *
     * @param  \XLite\Model\ViewList $other Value source
     */
    public function mapOverrides(\XLite\Model\ViewList $other)
    {
        if ($other) {
            $this->setListOverride($other->getListOverride());
            $this->setWeightOverride($other->getWeightOverride());
            $this->setOverrideMode($other->getOverrideMode());
        }
    }

    /**
     * Check if module for list item is enabled
     *
     * @return bool
     */
    public function isViewListModuleEnabled()
    {
        $class = $this->getChild();
        $tpl = $this->getTpl();

        if (
            $class
            && $moduleId = Module::getModuleIdByClassName($class)
        ) {
            return Manager::getRegistry()->isModuleEnabled($moduleId);
        }

        if (
            $tpl
            && preg_match('#modules/(\w+)/(\w+)/#S', $tpl, $match)
            && !Manager::getRegistry()->isModuleEnabled($match[1], $match[2])
        ) {
            return false;
        }

        return true;
    }

    /**
     * Set override_mode
     *
     * @param integer $overrideMode
     * @return ViewList
     */
    public function setOverrideMode($overrideMode)
    {
        $this->override_mode = $overrideMode;
        return $this;
    }

    /**
     * Get override_mode
     *
     * @return integer
     */
    public function getOverrideMode()
    {
        return $this->override_mode;
    }
}
