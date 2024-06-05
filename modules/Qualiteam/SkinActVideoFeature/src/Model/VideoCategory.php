<?php
/*
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 *  See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActVideoFeature\Model;

use Doctrine\ORM\Mapping as ORM;
use Qualiteam\SkinActVideoFeature\Model\Repo\EducationalVideo as EducationalVideoRepo;
use Qualiteam\SkinActVideoFeature\Model\EducationalVideo as EducationalVideoModel;
use XLite\Core\Database;

/**
 * @ORM\Entity
 * @ORM\Table  (name="video_categories",
 *      indexes={
 *          @ORM\Index (name="lpos", columns={"lpos"}),
 *          @ORM\Index (name="rpos", columns={"rpos"}),
 *          @ORM\Index (name="enabled", columns={"enabled"})
 *      }
 * )
 */
class VideoCategory extends \XLite\Model\Base\I18n
{
    /**
     * Node unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Node left value
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $lpos;

    /**
     * Node right value
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $rpos;

    /**
     * Node status
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Category "depth" in the tree
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $depth = -1;

    /**
     * Category position parameter. Sort inside the parent category
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $pos = 0;

    /**
     * Some cached flags
     *
     * @var QuickFlags
     *
     * @ORM\OneToOne (targetEntity="Qualiteam\SkinActVideoFeature\Model\QuickFlags", mappedBy="category", cascade={"all"})
     */
    protected $quickFlags;

    /**
     * One-to-one relation with video_category_images table
     *
     * @var VideoCategory
     *
     * @ORM\OneToOne  (targetEntity="Qualiteam\SkinActVideoFeature\Model\Image", mappedBy="category", cascade={"all"})
     */
    protected $image;

    /**
     * Relation to a CategoryVideos entities
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActVideoFeature\Model\CategoryVideos", mappedBy="category", cascade={"all"})
     * @ORM\OrderBy   ({"orderby" = "ASC"})
     */
    protected $categoryVideos;

    /**
     * Child categories
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActVideoFeature\Model\VideoCategory", mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"pos" = "ASC","id"="ASC","lpos" = "ASC"})
     */
    protected $children;

    /**
     * Parent category
     *
     * @var VideoCategory
     *
     * @ORM\ManyToOne  (targetEntity="Qualiteam\SkinActVideoFeature\Model\VideoCategory", inversedBy="children")
     * @ORM\JoinColumn (name="parent_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * Caching flag to check if the category is visible in the parents branch.
     *
     * @var boolean
     */
    protected $flagVisible;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActVideoFeature\Model\VideoCategoryTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Set parent
     *
     * @param VideoCategory $parent Parent category OPTIONAL
     *
     * @return void
     */
    public function setParent(VideoCategory $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Set image
     *
     * @param \Qualiteam\SkinActVideoFeature\Model\Image $image Image OPTIONAL
     *
     * @return void
     */
    public function setImage(\Qualiteam\SkinActVideoFeature\Model\Image $image = null)
    {
        $this->image = $image;
    }

    /**
     * Check every parent of category to be enabled.
     *
     * @return boolean
     */
    public function isVisible()
    {
        if ($this->flagVisible === null) {
            $current = $this;
            $hidden = false;
            $rootCategoryId = Database::getRepo(VideoCategory::class)->getRootCategoryId();

            while ($rootCategoryId != $current->getCategoryId()) {
                if (!$this->checkStorefrontVisibility($current)) {
                    $hidden = true;
                    break;
                }
                $current = $current->getParent();
            }
            $this->flagVisible = !$hidden;
        }

        return $this->flagVisible;
    }

    protected function checkStorefrontVisibility($current)
    {
        return $current->getEnabled();
    }

    /**
     * @return bool
     */
    public function isRootCategory()
    {
        return $this->getCategoryId() == Database::getRepo(VideoCategory::class)->getRootCategoryId();
    }

    /**
     * Get the number of subcategories
     *
     * @return integer
     */
    public function getSubcategoriesCount()
    {
        $result = 0;

        $enabledCondition = $this->getRepository()->getEnabledCondition();
        $quickFlags = $this->getQuickFlags();

        if ($quickFlags) {
            $result = $enabledCondition
                ? $quickFlags->getSubcategoriesCountEnabled()
                : $quickFlags->getSubcategoriesCountAll();
        }

        return $result;
    }

    /**
     * Return subcategories list
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubcategories()
    {
        return $this->getChildren()->filter(
            static function (\Qualiteam\SkinActVideoFeature\Model\VideoCategory $category) {
                return $category->getEnabled();
            }
        );
    }

    /**
     * Check if category has subcategories
     *
     * @return boolean
     */
    public function hasSubcategories()
    {
        return 0 < $this->getSubcategoriesCount();
    }

    /**
     * Get category path
     *
     * @return array
     */
    public function getPath()
    {
        return $this->getRepository()->getCategoryPath($this->getCategoryId());
    }

    /**
     * Gets full path to the category as a string: <parent category>/.../<category name>
     *
     * @return string
     */
    public function getStringPath()
    {
        $path = [];

        foreach ($this->getPath() as $category) {
            $path[] = $category->getName();
        }

        return implode('/', $path);
    }

    /**
     * Return parent category ID
     *
     * @return integer
     */
    public function getParentId()
    {
        return $this->getParent() ? $this->getParent()->getCategoryId() : 0;
    }

    /**
     * Set parent category ID
     *
     * @param integer $parentID Value to set
     *
     * @return void
     */
    public function setParentId($parentID)
    {
        $this->parent = $this->getRepository()->find($parentID);
    }

    /**
     * Return number of videos associated with the category
     *
     * @return integer
     */
    public function getVideosCount()
    {
        return $this->getVideos(null, true);
    }

    /**
     * Return videos list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition OPTIONAL
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    public function getVideos(\XLite\Core\CommonCell $cnd = null, $countOnly = false)
    {
        if ($cnd === null) {
            $cnd = new \XLite\Core\CommonCell();
        }

        $cnd->{EducationalVideoRepo::P_CATEGORY_ID} = $this->getCategoryId();

        return Database::getRepo(EducationalVideoModel::class)->search($cnd, $countOnly);
    }

    /**
     * Check if video present in category
     *
     * @param EducationalVideoModel|integer $video Video
     *
     * @return boolean
     */
    public function hasVideo($video)
    {
        return $this->getRepository()->hasVideo($this, $video);
    }

    /**
     * Get position
     *
     * @return integer
     */
    public function getPosition()
    {
        return $this->getPos();
    }

    /**
     * Set position
     *
     * @param integer $position Video position
     *
     * @return self
     */
    public function setPosition($position)
    {
        return $this->setPos($position);
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->categoryVideos = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children         = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    public function getCategoryId()
    {
        return $this->getId();
    }

    /**
     * Get category_id
     *
     * @return integer
     */
    public function getId()
    {
        return (int) $this->id;
    }

    /**
     * Set lpos
     *
     * @param integer $lpos
     *
     * @return VideoCategory
     */
    public function setLpos($lpos)
    {
        $this->lpos = $lpos;
        return $this;
    }

    /**
     * Get lpos
     *
     * @return integer
     */
    public function getLpos()
    {
        return $this->lpos;
    }

    /**
     * Set rpos
     *
     * @param integer $rpos
     *
     * @return VideoCategory
     */
    public function setRpos($rpos)
    {
        $this->rpos = $rpos;
        return $this;
    }

    /**
     * Get rpos
     *
     * @return integer
     */
    public function getRpos()
    {
        return $this->rpos;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     *
     * @return VideoCategory
     */
    public function setEnabled($enabled)
    {
        $this->getPreviousState()->enabled = $this->enabled;
        $this->enabled                     = (bool)$enabled;

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
     * Set depth
     *
     * @param integer $depth
     *
     * @return VideoCategory
     */
    public function setDepth($depth)
    {
        $this->depth = $depth;
        return $this;
    }

    /**
     * Get depth
     *
     * @return integer
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Set pos
     *
     * @param integer $pos
     *
     * @return VideoCategory
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
        return $this;
    }

    /**
     * Get pos
     *
     * @return integer
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * Set quickFlags
     *
     * @param QuickFlags|null $quickFlags
     *
     * @return VideoCategory
     */
    public function setQuickFlags(QuickFlags $quickFlags = null)
    {
        $this->quickFlags = $quickFlags;
        return $this;
    }

    /**
     * Get quickFlags
     *
     * @return QuickFlags
     */
    public function getQuickFlags()
    {
        return $this->quickFlags;
    }

    /**
     * Get image
     *
     * @return VideoCategory
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add categoryVideos
     *
     * @param CategoryVideos $categoryVideos
     *
     * @return VideoCategory
     */
    public function addCategoryVideos(CategoryVideos $categoryVideos)
    {
        $this->categoryVideos[] = $categoryVideos;
        return $this;
    }

    /**
     * Get categoryVideos
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoryVideos()
    {
        return $this->categoryVideos;
    }

    public function getCategoryEnabledVideos()
    {
        return $this->getCategoryVideos()->filter(
            static function (CategoryVideos $categoryVideos) {
                return $categoryVideos->getVideo()->getEnabled();
            }
        );
    }

    /**
     * Add children
     *
     * @param VideoCategory $children
     *
     * @return VideoCategory
     */
    public function addChildren(VideoCategory $children)
    {
        $this->children[] = $children;
        return $this;
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Get parent
     *
     * @return VideoCategory
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Get front URL
     *
     * @return string
     */
    public function getFrontURL($buildCuInAdminZone = false)
    {
        return $this->getCategoryId()
            ? \XLite\Core\Converter::makeURLValid(
                \XLite::getInstance()->getShopURL(
                    \XLite\Core\Converter::buildURL(
                        'video_category',
                        '',
                        ['id' => $this->getId()],
                        \XLite::getCustomerScript(),
                        $buildCuInAdminZone
                    )
                )
            )
            : null;
    }

    public function isSecondLevelSubcategory()
    {
        return $this->getDepth() >= 1;
    }
}