<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * SearchFilter model (is used to save search forms parameters)
 *
 * @ORM\Entity
 * @ORM\Table  (name="search_filters",
 *      indexes={
 *          @ORM\Index (name="filterKey", columns={"filterKey"}),
 *      }
 * )
 */
class SearchFilter extends \XLite\Model\Base\I18n
{
    /**
     * Filter unique id
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Filter key: identifies the filter location (target + items list)
     *
     * @var string
     *
     * @ORM\Column (type="string")
     */
    protected $filterKey = '';

    /**
     * Filter parameters (serialized array)
     *
     * @var array
     *
     * @ORM\Column (type="array")
     */
    protected $parameters = '';

    /**
     * Filter name suffix (used to add some additional data to the filter name, e.g. counter)
     *
     * @var string
     */
    protected $suffix = '';

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XLite\Model\SearchFilterTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

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
     * Set filterKey
     *
     * @param string $filterKey
     * @return SearchFilter
     */
    public function setFilterKey($filterKey)
    {
        $this->filterKey = $filterKey;
        return $this;
    }

    /**
     * Get filterKey
     *
     * @return string
     */
    public function getFilterKey()
    {
        return $this->filterKey;
    }

    /**
     * Set parameters
     *
     * @param array $parameters
     * @return SearchFilter
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
        return $this;
    }

    /**
     * Get parameters
     *
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }
}
