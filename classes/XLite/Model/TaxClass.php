<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use XLite\API\Endpoint\TaxClass\DTO\TaxClassInput;
use XLite\API\Endpoint\TaxClass\DTO\TaxClassOutput;
use XLite\API\Filter\AlphabeticalOrderFilter;

/**
 * Tax class
 *
 * @ORM\Entity
 * @ORM\Table  (name="tax_classes")
 * @ApiPlatform\ApiResource(
 *     shortName="Tax Class",
 *     input=TaxClassInput::class,
 *     output=TaxClassOutput::class,
 *     itemOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/tax_classes/{id}",
 *             "identifiers"={"id"},
 *         },
 *         "put"={
 *             "method"="PUT",
 *             "path"="/tax_classes/{id}",
 *             "identifiers"={"id"},
 *         },
 *         "delete"={
 *             "method"="DELETE",
 *             "path"="/tax_classes/{id}",
 *             "identifiers"={"id"},
 *         }
 *     },
 *     collectionOperations={
 *         "get"={
 *             "method"="GET",
 *             "path"="/tax_classes",
 *             "identifiers"={"id"},
 *         },
 *         "post"={
 *             "method"="POST",
 *             "path"="/tax_classes",
 *             "identifiers"={"id"},
 *         }
 *     }
 * )
 * @ApiPlatform\ApiFilter(AlphabeticalOrderFilter::class, properties={"name"="ASC"})
 */
class TaxClass extends \XLite\Model\Base\I18n
{
    /**
     * ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Position
     *
     * @var   integer
     * @see   ____var_see____
     * @since 1.0.0
     *
     * @ORM\Column (type="integer")
     */
    protected $position = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\TaxClassTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

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
        if (!isset($cnd)) {
            $cnd = new \XLite\Core\CommonCell();
        }

        // Main condition for this search
        $cnd->{\XLite\Model\Repo\Product::P_TAX_CLASS} = $this;

        return \XLite\Core\Database::getRepo('XLite\Model\Product')->search($cnd, $countOnly);
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
     * @return TaxClass
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
}
