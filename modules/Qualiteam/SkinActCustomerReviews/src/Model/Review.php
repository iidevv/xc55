<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCustomerReviews\Model;

use Doctrine\ORM\Mapping as ORM;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 */
class Review extends \XC\Reviews\Model\Review
{

    /**
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $title = '';

    /**
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $advantages = '';

    /**
     * @var string
     *
     * @ORM\Column (type="text", nullable=true)
     */
    protected $disadvantages = '';

    /**
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true, options={"default":0})
     */
    protected $useful = 0;

    /**
     * @var integer
     *
     * @ORM\Column (type="integer", nullable=true, options={"default":0})
     */
    protected $nonUseful = 0;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="Qualiteam\SkinActCustomerReviews\Model\ReviewFile", mappedBy="review", cascade={"all"})
     * @ORM\OrderBy   ({"id" = "ASC"})
     */
    protected $files;

    /**
     * @return int
     */
    public function getNonUseful()
    {
        return $this->nonUseful;
    }

    /**
     * @param int $nonUseful
     */
    public function setNonUseful($nonUseful)
    {
        $this->nonUseful = $nonUseful;
    }



    public function addFiles($file)
    {
        $this->files[] = $file;

        return $this;
    }

    /**
     * Get images
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getFiles()
    {
        return $this->files;
    }

    public function __construct(array $data = [])
    {
        $this->files = new \Doctrine\Common\Collections\ArrayCollection();

        parent::__construct($data);
    }

    /**
     * @return string
     */
    public function getAdvantages()
    {
        return $this->advantages;
    }

    /**
     * @param string $advantages
     */
    public function setAdvantages($advantages)
    {
        $this->advantages = $advantages;
    }

    /**
     * @return string
     */
    public function getDisadvantages()
    {
        return $this->disadvantages;
    }

    /**
     * @param string $disadvantages
     */
    public function setDisadvantages($disadvantages)
    {
        $this->disadvantages = $disadvantages;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return int
     */
    public function getUseful()
    {
        return $this->useful;
    }

    /**
     * @param int $useful
     */
    public function setUseful($useful)
    {
        $this->useful = $useful;
    }


}