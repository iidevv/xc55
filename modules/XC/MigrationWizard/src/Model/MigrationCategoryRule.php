<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Migration Category Rules
 *
 * @ORM\Entity
 * @ORM\Table (name="migration_wizard_category_rules",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint (name="pair", columns={"categoryId","ruleId"})
 *      },
 *      indexes={
 *          @ORM\Index (name="orderby", columns={"pos"})
 *      }
 * )
 */
class MigrationCategoryRule extends \XLite\Model\AEntity
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $id;

    /**
     * Rule position in the category
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $pos = 0;

    /**
     * Relation to a category entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne  (targetEntity="XC\MigrationWizard\Model\MigrationCategory", inversedBy="rules")
     * @ORM\JoinColumn (name="categoryId", referencedColumnName="categoryId", onDelete="CASCADE")
     */
    protected $category;

    /**
     * Relation to a rule entity
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToOne  (targetEntity="XC\MigrationWizard\Model\MigrationRule", inversedBy="categories")
     * @ORM\JoinColumn (name="ruleId", referencedColumnName="ruleId", onDelete="CASCADE")
     */
    protected $rule;

    /**
     * Returns unique ID
     *
     * @see \XLite\Model\AEntity->getUniqueIdentifier()
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * @param int $pos
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRule()
    {
        return $this->rule;
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $rule
     */
    public function setRule($rule)
    {
        $this->rule = $rule;
    }
}
