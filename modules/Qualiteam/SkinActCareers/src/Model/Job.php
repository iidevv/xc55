<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActCareers\Model;


use Doctrine\ORM\Mapping as ORM;

/**
 * Class Job
 * @ORM\Entity
 * @ORM\Table  (name="careers_job")
 */
class Job extends \XLite\Model\AEntity
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column (type="integer", options={ "unsigned": true })
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column (type="text", nullable=true)
     *
     * @var string
     */
    protected $title;

    /**
     * @ORM\Column (type="text", nullable=true)
     *
     * @var string
     */
    protected $briefDescription;

    /**
     * @ORM\Column (type="text", nullable=true)
     *
     * @var string
     */
    protected $pageDescription;

    /**
     * @ORM\Column (type="text", nullable=true)
     *
     * @var string
     */
    protected $duties;

    /**
     * @ORM\Column (type="text", nullable=true)
     *
     * @var string
     */
    protected $requirements;

    /**
     * @ORM\Column (type="text", nullable=true)
     *
     * @var string
     */
    protected $compensation;

    /**
     * @ORM\Column (type="text", nullable=true)
     *
     * @var string
     */
    protected $employmentType;

    /**
     * @ORM\Column (type="text", nullable=true)
     *
     * @var string
     */
    protected $probationTime;

    /**
     * @ORM\Column (type="integer", options={ "unsigned": true }, nullable=true)
     *
     * @var int
     */
    protected $publicationDate;

    /**
     * @ORM\Column (type="boolean", options={ "default": true })
     *
     * @var bool
     */
    protected $enabled = true;


    /**
     * @ORM\Column (type="integer", options={ "default": 0 })
     *
     * @var int
     */
    protected $position = 0;

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @param int $position
     */
    public function setPosition($position)
    {
        $this->position = (int)$position;
    }

    /**
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
    }


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @return string
     */
    public function getBriefDescription()
    {
        return $this->briefDescription;
    }

    /**
     * @param string $briefDescription
     */
    public function setBriefDescription($briefDescription)
    {
        $this->briefDescription = $briefDescription;
    }

    /**
     * @return string
     */
    public function getPageDescription()
    {
        return $this->pageDescription;
    }

    /**
     * @param string $pageDescription
     */
    public function setPageDescription($pageDescription)
    {
        $this->pageDescription = $pageDescription;
    }

    /**
     * @return string
     */
    public function getDuties()
    {
        return $this->duties;
    }

    /**
     * @param string $duties
     */
    public function setDuties($duties)
    {
        $this->duties = $duties;
    }

    /**
     * @return string
     */
    public function getRequirements()
    {
        return $this->requirements;
    }

    /**
     * @param string $requirements
     */
    public function setRequirements($requirements)
    {
        $this->requirements = $requirements;
    }

    /**
     * @return string
     */
    public function getCompensation()
    {
        return $this->compensation;
    }

    /**
     * @param string $compensation
     */
    public function setCompensation($compensation)
    {
        $this->compensation = $compensation;
    }

    /**
     * @return string
     */
    public function getEmploymentType()
    {
        return $this->employmentType;
    }

    /**
     * @param string $employmentType
     */
    public function setEmploymentType($employmentType)
    {
        $this->employmentType = $employmentType;
    }

    /**
     * @return string
     */
    public function getProbationTime()
    {
        return $this->probationTime;
    }

    /**
     * @param string $probationTime
     */
    public function setProbationTime($probationTime)
    {
        $this->probationTime = $probationTime;
    }

    /**
     * @return int
     */
    public function getPublicationDate()
    {
        return $this->publicationDate;
    }

    /**
     * @param int $publicationDate
     */
    public function setPublicationDate($publicationDate)
    {
        $this->publicationDate = (int)$publicationDate;
    }


}