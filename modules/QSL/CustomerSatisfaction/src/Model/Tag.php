<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Tag
 *
 * @ORM\Entity (repositoryClass="\QSL\CustomerSatisfaction\Model\Repo\Tag")
 *
 * @ORM\Table  (name="cs_tags",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="id", columns={"id"})
 *      },
 *      indexes={
 *          @ORM\Index (name="name",     columns={"name"}),
 *      }
 * )
 *
 * @ORM\MappedSuperclass
 */
class Tag extends \XLite\Model\AEntity
{
    /**
     * Unique tag ID
     *
     * @var mixed
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Relation to a TagSurvey entities
     *
     * @var \QSL\CustomerSatisfaction\Model\Survey
     *
     * @ORM\ManyToMany (targetEntity="QSL\CustomerSatisfaction\Model\Survey", inversedBy="tags", cascade={"all"})     *
     */
    protected $surveys;

    /**
     * Question text
     *
     * @var string
     *
     * @ORM\Column (type="string", length=128, nullable=true)
     */
    protected $name = '';


    /**
     * Gets the Unique tag ID.
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the Unique tag ID.
     *
     * @param mixed $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the Relation to a TagSurvey entities.
     *
     * @return \QSL\CustomerSatisfaction\Model\Survey
     */
    public function getSurveys()
    {
        return $this->surveys;
    }

    /**
     * Sets the Relation to a TagSurvey entities.
     *
     * @param \QSL\CustomerSatisfaction\Model\Survey $surveys the surveys
     *
     * @return self
     */
    public function setSurveys(\QSL\CustomerSatisfaction\Model\Survey $surveys)
    {
        $this->surveys = $surveys;

        return $this;
    }

    /**
     * Gets the Question text.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the Question text.
     *
     * @param string $name the name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }
}
