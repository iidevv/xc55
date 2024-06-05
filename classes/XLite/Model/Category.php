<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\Category\DTO\CategoryMoveInput;
use XLite\API\Endpoint\Category\DTO\CategoryProductInput;
use XLite\API\Endpoint\Category\DTO\CategoryProductOutput;
use XLite\API\Endpoint\Category\DTO\CategoryStatsOutput;
use XLite\API\Endpoint\Category\DTO\Input as CategoryInput;
use XLite\API\Endpoint\Category\DTO\Output as CategoryOutput;
use XLite\API\Endpoint\Category\Filter\ParentFilter;
use XLite\API\Filter\TranslationAwareOrderFilter;
use XLite\Controller\API\Category\DeleteCategoryProduct;
use XLite\Core\Database;

/**
 * Category
 *
 * @ORM\Entity
 * @ORM\Table  (name="categories",
 *      indexes={
 *          @ORM\Index (name="lpos", columns={"lpos"}),
 *          @ORM\Index (name="rpos", columns={"rpos"}),
 *          @ORM\Index (name="enabled", columns={"enabled"})
 *      }
 * )
 * @ApiPlatform\ApiResource(
 *     input=CategoryInput::class,
 *     output=CategoryOutput::class,
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/categories/{category_id}",
 *             "identifiers"={"category_id"},
 *         },
 *         "put"={
 *             "method"="PUT",
 *             "path"="/categories/{category_id}",
 *             "identifiers"={"category_id"},
 *         },
 *         "delete"={
 *             "method"="DELETE",
 *             "path"="/categories/{category_id}",
 *             "identifiers"={"category_id"},
 *         },
 *         "move"={
 *             "method"="PUT",
 *             "input"=CategoryMoveInput::class,
 *             "path"="/categories/{category_id}/move",
 *             "identifiers"={"category_id"},
 *             "openapi_context"={
 *                  "summary"="Update a category position in the categories tree",
 *                  "parameters"={
 *                      {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *             },
 *         },
 *         "stats"={
 *             "method"="GET",
 *             "output"=CategoryStatsOutput::class,
 *             "path"="/categories/{category_id}/stats",
 *             "identifiers"={"category_id"},
 *             "openapi_context"={
 *                  "summary"="Retrieve category statistics",
 *                  "parameters"={
 *                      {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                  }
 *             },
 *         },
 *         "delete_category_product"={
 *             "method"="DELETE",
 *             "path"="/categories/{category_id}/products/{product_id}",
 *             "identifiers"={"category_id"},
 *             "requirements"={"category_id"="\d+", "product_id"="\d+"},
 *             "controller"=DeleteCategoryProduct::class,
 *             "read"=false,
 *             "openapi_context"={
 *                  "summary"="Delete a product from a category",
 *                  "parameters"={
 *                      {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                      {"name"="product_id", "in"="path", "required"=true, "schema"={"type"="integer"}},
 *                  }
 *             },
 *         },
 *     },
 *     collectionOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/categories",
 *             "identifiers"={"category_id"},
 *         },
 *         "post"={
 *             "method"="POST",
 *             "path"="/categories",
 *             "identifiers"={"category_id"},
 *         },
 *         "add_category_product"={
 *             "method"="POST",
 *             "input"=CategoryProductInput::class,
 *             "path"="/categories/{category_id}/products",
 *             "identifiers"={"category_id"},
 *             "requirements"={"category_id"="\d+"},
 *             "openapi_context"={
 *                 "summary"="Add a product to a category",
 *                 "parameters"={
 *                     {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                 }
 *             },
 *         },
 *         "get_category_products"={
 *             "method"="GET",
 *             "path"="/categories/{category_id}/products",
 *             "identifiers"={"category_id"},
 *             "output"=CategoryProductOutput::class,
 *             "requirements"={"category_id"="\d+"},
 *             "openapi_context"={
 *                 "summary"="Retrieve a list of products from a category",
 *                 "parameters"={
 *                     {"name"="category_id", "in"="path", "required"=true, "schema"={"type"="integer"}}
 *                 }
 *             },
 *         },
 *     }
 * )
 * @ApiPlatform\ApiFilter(ParentFilter::class, properties={"parent"})
 * @ApiPlatform\ApiFilter(TranslationAwareOrderFilter::class, properties={"position"="ASC"})
 */
class Category extends \XLite\Model\Base\Catalog
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
    protected $category_id;

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
     * Whether to display the category title, or not
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $show_title = true;

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
     * Whether to display the category title, or not
     *
     * @var string
     *
     * @ORM\Column (type="string", length=32, nullable=true)
     */
    protected $root_category_look;

    /**
     * Some cached flags
     *
     * @var \XLite\Model\Category\QuickFlags
     *
     * @ORM\OneToOne (targetEntity="XLite\Model\Category\QuickFlags", mappedBy="category", cascade={"all"})
     */
    protected $quickFlags;

    /**
     * Memberships
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Membership", inversedBy="categories")
     * @ORM\JoinTable (name="category_membership_links",
     *      joinColumns={@ORM\JoinColumn (name="category_id", referencedColumnName="category_id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn (name="membership_id", referencedColumnName="membership_id", onDelete="CASCADE")}
     * )
     */
    protected $memberships;

    /**
     * One-to-one relation with category_images table
     *
     * @var \XLite\Model\Image\Category\Image
     *
     * @ORM\OneToOne  (targetEntity="XLite\Model\Image\Category\Image", mappedBy="category", cascade={"all"})
     */
    protected $image;

    /**
     * One-to-one relation with category_images table
     *
     * @var \XLite\Model\Image\Category\Banner
     *
     * @ORM\OneToOne  (targetEntity="XLite\Model\Image\Category\Banner", mappedBy="category", cascade={"all"})
     */
    protected $banner;

    /**
     * Relation to a CategoryProducts entities
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\CategoryProducts", mappedBy="category", cascade={"all"})
     * @ORM\OrderBy   ({"orderby" = "ASC"})
     */
    protected $categoryProducts;

    /**
     * Child categories
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\Category", mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"pos" = "ASC","category_id"="ASC","lpos" = "ASC"})
     */
    protected $children;

    /**
     * Parent category
     *
     * @var \XLite\Model\Category
     *
     * @ORM\ManyToOne  (targetEntity="XLite\Model\Category", inversedBy="children")
     * @ORM\JoinColumn (name="parent_id", referencedColumnName="category_id", onDelete="SET NULL")
     */
    protected $parent;

    /**
     * Caching flag to check if the category is visible in the parents branch.
     *
     * @var boolean
     */
    protected $flagVisible;

    /**
     * Clean URLs
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\CleanURL", mappedBy="category", cascade={"all"})
     * @ORM\OrderBy   ({"id" = "ASC"})
     */
    protected $cleanURLs;

    /**
     * Meta description type
     *
     * @var string
     *
     * @ORM\Column (type="string", length=1)
     */
    protected $metaDescType = 'A';

    /**
     * Flag to exporting entities
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $xcPendingExport = false;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\CategoryTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Get object unique id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->getCategoryId();
    }

    /**
     * Set parent
     *
     * @param \XLite\Model\Category $parent Parent category OPTIONAL
     *
     * @return void
     */
    public function setParent(\XLite\Model\Category $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Set image
     *
     * @param \XLite\Model\Image\Category\Image $image Image OPTIONAL
     *
     * @return void
     */
    public function setImage(\XLite\Model\Image\Category\Image $image = null)
    {
        $this->image = $image;
    }

    /**
     * Check if category has image
     *
     * @return boolean
     */
    public function hasImage()
    {
        return $this->getImage() !== null;
    }

    /**
     * Set banner image
     *
     * @param \XLite\Model\Image\Category\Banner $image Image OPTIONAL
     *
     * @return void
     */
    public function setBanner(\XLite\Model\Image\Category\Banner $image = null)
    {
        $this->banner = $image;
    }

    /**
     * Check if category has image
     *
     * @return boolean
     */
    public function hasBanner()
    {
        return $this->getBanner() !== null;
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
            $rootCategoryId = Database::getRepo('XLite\Model\Category')->getRootCategoryId();

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

    /**
     * @return bool
     */
    public function isRootCategory()
    {
        return $this->getCategoryId() == Database::getRepo('XLite\Model\Category')->getRootCategoryId();
    }

    /**
     * Check if the category is visible on the storefront for the current customer
     *
     * @param \XLite\Model\Category $current Current category
     *
     * @return boolean
     */
    protected function checkStorefrontVisibility($current)
    {
        return $current->getEnabled()
            && (
                $current->getMemberships()->count() === 0
                || in_array(\XLite\Core\Auth::getInstance()->getMembershipId(), $current->getMembershipIds())
            );
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
     * Check if category has subcategories
     *
     * @return boolean
     */
    public function hasSubcategories()
    {
        return 0 < $this->getSubcategoriesCount();
    }

    /**
     * Return subcategories list
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSubcategories()
    {
        return $this->getChildren()->filter(
            static function (\XLite\Model\Category $category) {
                return $category->getEnabled();
            }
        );
    }

    /**
     * Return siblings list.
     * You are able to include itself into this list. (Customer area)
     *
     * @param boolean $hasSelf Flag to include itself
     *
     * @return array
     */
    public function getSiblings($hasSelf = false)
    {
        return $this->getRepository()->getSiblings($this, $hasSelf);
    }

    /**
     * Return siblings list.
     * You are able to include itself into this list. (Customer area)
     *
     * @param integer $maxResults   Max results
     * @param boolean $hasSelf      Flag to include itself
     *
     * @return array
     */
    public function getSiblingsFramed($maxResults, $hasSelf = false)
    {
        return $this->getRepository()->getSiblingsFramed($this, $maxResults, $hasSelf);
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
     * Get membership Ids
     *
     * @return array
     */
    public function getMembershipIds()
    {
        $result = [];

        foreach ($this->getMemberships() as $membership) {
            $result[] = $membership->getMembershipId();
        }

        return $result;
    }

    /**
     * Flag if the category and active profile have the same memberships. (when category is displayed or hidden)
     *
     * @return boolean
     */
    public function hasAvailableMembership()
    {
        return $this->getMemberships()->count() === 0
            || in_array(\XLite\Core\Auth::getInstance()->getMembershipId(), $this->getMembershipIds());
    }

    /**
     * Return number of products associated with the category
     *
     * @return integer
     */
    public function getProductsCount()
    {
        return $this->getProducts(null, true);
    }

    /**
     * Return products list
     *
     * @param \XLite\Core\CommonCell $cnd       Search condition OPTIONAL
     * @param boolean                $countOnly Return items list or only its size OPTIONAL
     *
     * @return array|integer
     */
    public function getProducts(\XLite\Core\CommonCell $cnd = null, $countOnly = false)
    {
        if ($cnd === null) {
            $cnd = new \XLite\Core\CommonCell();
        }

        // Main condition for this search
        $cnd->{\XLite\Model\Repo\Product::P_CATEGORY_ID} = $this->getCategoryId();

        if (
            \XLite\Core\Config::getInstance()->General->show_out_of_stock_products !== 'directLink'
            && !'searchOnly' !== \XLite\Core\Config::getInstance()->General->show_out_of_stock_products
            && !\XLite::isAdminZone()
        ) {
            $cnd->{\XLite\Model\Repo\Product::P_INVENTORY} = false;
        }

        return Database::getRepo('XLite\Model\Product')->search($cnd, $countOnly);
    }

    /**
     * Check if product present in category
     *
     * @param \XLite\Model\Product|integer $product  Product
     *
     * @return boolean
     */
    public function hasProduct($product)
    {
        return $this->getRepository()->hasProduct($this, $product);
    }

    /**
     * Return category description
     *
     * @return string
     */
    public function getViewDescription()
    {
        return static::getPreprocessedValue($this->getDescription())
            ?: $this->getDescription();
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
     * @param integer $position Product position
     *
     * @return self
     */
    public function setPosition($position)
    {
        return $this->setPos($position);
    }

    /**
     * Returns meta description
     *
     * @return string
     */
    public function getMetaDesc()
    {
        return $this->getMetaDescType() === 'A'
            ? static::postprocessMetaDescription($this->getDescription())
            : $this->getSoftTranslation()->getMetaDesc();
    }

    /**
     * Returns meta description type
     *
     * @return string
     */
    public function getMetaDescType()
    {
        $result = $this->metaDescType;

        if (!$result) {
            $metaDescPresent = array_reduce($this->getTranslations()->toArray(), static function ($carry, $item) {
                return $carry ?: (bool) $item->getMetaDesc();
            }, false);

            $result = $metaDescPresent ? 'C' : 'A';
        }

        return $result;
    }

    /**
     * Constructor
     *
     * @param array $data Entity properties OPTIONAL
     */
    public function __construct(array $data = [])
    {
        $this->categoryProducts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children         = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * Get category_id
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return (int) $this->category_id;
    }

    /**
     * Set lpos
     *
     * @param integer $lpos
     * @return Category
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
     * @return Category
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
     * @return Category
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
     * Set show_title
     *
     * @param boolean $showTitle
     * @return Category
     */
    public function setShowTitle($showTitle)
    {
        $this->show_title = $showTitle;
        return $this;
    }

    /**
     * Get show_title
     *
     * @return boolean
     */
    public function getShowTitle()
    {
        return $this->show_title;
    }

    /**
     * Set depth
     *
     * @param integer $depth
     * @return Category
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
     * @return Category
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
     * Set root_category_look
     *
     * @param string $rootCategoryLook
     * @return Category
     */
    public function setRootCategoryLook($rootCategoryLook)
    {
        $this->root_category_look = $rootCategoryLook;
        return $this;
    }

    /**
     * Get root_category_look
     *
     * @return string
     */
    public function getRootCategoryLook()
    {
        return $this->root_category_look;
    }

    /**
     * Set metaDescType
     *
     * @param string $metaDescType
     * @return Category
     */
    public function setMetaDescType($metaDescType)
    {
        $this->metaDescType = $metaDescType;
        return $this;
    }

    /**
     * Set xcPendingExport
     *
     * @param boolean $xcPendingExport
     * @return Category
     */
    public function setXcPendingExport($xcPendingExport)
    {
        $this->xcPendingExport = $xcPendingExport;
        return $this;
    }

    /**
     * Get xcPendingExport
     *
     * @return boolean
     */
    public function getXcPendingExport()
    {
        return $this->xcPendingExport;
    }

    /**
     * Set quickFlags
     *
     * @param \XLite\Model\Category\QuickFlags $quickFlags
     * @return Category
     */
    public function setQuickFlags(\XLite\Model\Category\QuickFlags $quickFlags = null)
    {
        $this->quickFlags = $quickFlags;
        return $this;
    }

    /**
     * Get quickFlags
     *
     * @return \XLite\Model\Category\QuickFlags
     */
    public function getQuickFlags()
    {
        return $this->quickFlags;
    }

    /**
     * Add memberships
     *
     * @param \XLite\Model\Membership $memberships
     * @return Category
     */
    public function addMemberships(\XLite\Model\Membership $memberships)
    {
        $this->memberships[] = $memberships;
        return $this;
    }

    /**
     * Get memberships
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMemberships()
    {
        return $this->memberships;
    }

    /**
     * Get image
     *
     * @return \XLite\Model\Image\Category\Image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get banner image
     *
     * @return \XLite\Model\Image\Category\Banner
     */
    public function getBanner()
    {
        return $this->banner;
    }

    /**
     * Add categoryProducts
     *
     * @param \XLite\Model\CategoryProducts $categoryProducts
     * @return Category
     */
    public function addCategoryProducts(\XLite\Model\CategoryProducts $categoryProducts)
    {
        $this->categoryProducts[] = $categoryProducts;
        return $this;
    }

    /**
     * Get categoryProducts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategoryProducts()
    {
        return $this->categoryProducts;
    }

    /**
     * Add children
     *
     * @param \XLite\Model\Category $children
     * @return Category
     */
    public function addChildren(\XLite\Model\Category $children)
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
     * @return \XLite\Model\Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add cleanURLs
     *
     * @param \XLite\Model\CleanURL $cleanURLs
     * @return Category
     */
    public function addCleanURLs(\XLite\Model\CleanURL $cleanURLs)
    {
        $this->cleanURLs[] = $cleanURLs;
        return $this;
    }

    /**
     * Get cleanURLs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCleanURLs()
    {
        return $this->cleanURLs;
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
                        'category',
                        '',
                        ['category_id' => $this->getCategoryId()],
                        \XLite::getCustomerScript(),
                        $buildCuInAdminZone
                    )
                )
            )
            : null;
    }

    // {{{ Translation Getters / setters

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $description
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setDescription($description)
    {
        return $this->setTranslationField(__FUNCTION__, $description);
    }

    /**
     * @return string
     */
    public function getMetaTags()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $metaTags
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setMetaTags($metaTags)
    {
        return $this->setTranslationField(__FUNCTION__, $metaTags);
    }

    /**
     * @param string $metaDesc
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setMetaDesc($metaDesc)
    {
        return $this->setTranslationField(__FUNCTION__, $metaDesc);
    }

    /**
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->getTranslationField(__FUNCTION__);
    }

    /**
     * @param string $metaTitle
     *
     * @return \XLite\Model\Base\Translation
     */
    public function setMetaTitle($metaTitle)
    {
        return $this->setTranslationField(__FUNCTION__, $metaTitle);
    }

    // }}}
}
