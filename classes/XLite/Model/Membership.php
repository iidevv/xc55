<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\Membership\DTO\MembershipInput;
use XLite\API\Endpoint\Membership\DTO\MembershipOutput;
use XLite\API\Filter\AlphabeticalOrderFilter;

/**
 * Membership
 *
 * @ORM\Entity
 * @ORM\Table (name="memberships")
 * @ApiPlatform\ApiResource(
 *     input=MembershipInput::class,
 *     output=MembershipOutput::class,
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/memberships/{membership_id}",
 *             "identifiers"={"membership_id"},
 *         },
 *         "put"={
 *             "method"="PUT",
 *             "path"="/memberships/{membership_id}",
 *             "identifiers"={"membership_id"},
 *         },
 *         "delete"={
 *             "method"="DELETE",
 *             "path"="/memberships/{membership_id}",
 *             "identifiers"={"membership_id"},
 *         }
 *     },
 *     collectionOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/memberships",
 *             "identifiers"={"membership_id"},
 *         },
 *         "post"={
 *             "method"="POST",
 *             "path"="/memberships",
 *             "identifiers"={"membership_id"},
 *         }
 *     }
 * )
 * @ApiPlatform\ApiFilter(AlphabeticalOrderFilter::class, properties={"name"="ASC"})
 */
class Membership extends \XLite\Model\Base\I18n
{
    /**
     * Unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $membership_id;

    /**
     * Position
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * Enabled status
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Quick data
     *
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\QuickData", mappedBy="membership", cascade={"all"}, fetch="LAZY")
     */
    protected $quickData;

    /**
     * Categories
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Category", mappedBy="memberships", fetch="LAZY")
     */
    protected $categories;

    /**
     * Products
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany (targetEntity="XLite\Model\Product", mappedBy="memberships", fetch="LAZY")
     */
    protected $products;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\MembershipTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Get membership_id
     *
     * @return integer
     */
    public function getMembershipId()
    {
        return $this->membership_id;
    }

    /**
     * Set position
     *
     * @param integer $position
     * @return Membership
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
     * @return Membership
     */
    public function setEnabled($enabled)
    {
        $this->enabled = (bool)$enabled;
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
     * Add quickData
     *
     * @param \XLite\Model\QuickData $quickData
     * @return Membership
     */
    public function addQuickData(\XLite\Model\QuickData $quickData)
    {
        $this->quickData[] = $quickData;
        return $this;
    }

    /**
     * Get quickData
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuickData()
    {
        return $this->quickData;
    }

    /**
     * Add categories
     *
     * @param \XLite\Model\Category $categories
     * @return Membership
     */
    public function addCategories(\XLite\Model\Category $categories)
    {
        $this->categories[] = $categories;
        return $this;
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Add products
     *
     * @param \XLite\Model\Product $products
     * @return Membership
     */
    public function addProducts(\XLite\Model\Product $products)
    {
        $this->products[] = $products;
        return $this;
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getProducts()
    {
        return $this->products;
    }
}
