<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Migration Category
 *
 * @ORM\Entity
 * @ORM\Table  (name="migration_wizard_categories")
 *
 * @ORM\HasLifecycleCallbacks
 */
class MigrationCategory extends \XLite\Model\Base\I18n
{
    /**
     * Category unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $categoryId;

    /**
     * Category sort order
     *
     * @var integer
     *
     * @ORM\Column (type="integer")
     */
    protected $pos = 0;

    /**
     * Category status
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * Relation to a MigrationCategoryRules entities
     *
     * @var \Doctrine\Common\Collections\ArrayCollection|MigrationCategoryRule[]
     *
     * @ORM\OneToMany (targetEntity="XC\MigrationWizard\Model\MigrationCategoryRule", mappedBy="category", cascade={"all"})
     * @ORM\OrderBy   ({"pos" = "ASC"})
     */
    protected $rules;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\MigrationWizard\Model\MigrationCategoryTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Returns unique ID
     *
     * @return integer
     */
    public function getCategoryId()
    {
        return $this->categoryId;
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
     * @return bool
     */
    public function isEnabled()
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
     * @return \Doctrine\Common\Collections\ArrayCollection|MigrationCategoryRule[]
     */
    public function getRules()
    {
        return $this->rules->filter(static function (MigrationCategoryRule $item) {
            return class_exists($item->getRule()->getLogic());
        });
    }

    /**
     * @param \Doctrine\Common\Collections\ArrayCollection $rules
     */
    public function setRules($rules)
    {
        $this->rules = $rules;
    }
}
