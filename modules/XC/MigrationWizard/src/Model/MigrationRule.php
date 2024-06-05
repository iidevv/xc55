<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Migration Rule
 *
 * @ORM\Entity
 * @ORM\Table  (name="migration_wizard_rules")
 *
 * @ORM\HasLifecycleCallbacks
 */
class MigrationRule extends \XLite\Model\Base\I18n
{
    /**
     * Rule unique ID
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer", options={ "unsigned": true })
     */
    protected $ruleId;

    /**
     * Flag (true if rule is 'system rule')
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $isSystem = false;

    /**
     * Relation to a CategoryRule entities
     *
     * @var \Doctrine\ORM\PersistentCollection
     *
     * @ORM\OneToMany (targetEntity="XC\MigrationWizard\Model\MigrationCategoryRule", mappedBy="rule", cascade={"all"})
     * @ORM\OrderBy   ({"pos" = "ASC"})
     */
    protected $categories;

    /**
     * Logic class
     *
     * @var \XC\MigrationWizard\Logic\Import\Processor\AProcessor
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $logic;

    /**
     * Rule status
     *
     * @var boolean
     *
     * @ORM\Column (type="boolean")
     */
    protected $enabled = true;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany (targetEntity="XC\MigrationWizard\Model\MigrationRuleTranslation", mappedBy="owner", cascade={"all"})
     */
    protected $translations;

    /**
     * Returns unique ID
     *
     * @see \XLite\Model\AEntity->getUniqueIdentifier()
     *
     * @return integer
     */
    public function getRuleId()
    {
        return $this->ruleId;
    }

    /**
     * @return bool
     */
    public function isSystem()
    {
        return $this->isSystem;
    }

    /**
     * @param bool $isSystem
     */
    public function setIsSystem($isSystem)
    {
        $this->isSystem = $isSystem;
    }

    /**
     * @return \Doctrine\ORM\PersistentCollection
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param \Doctrine\ORM\PersistentCollection $categories
     */
    public function setCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @return \XC\MigrationWizard\Logic\Import\Processor\AProcessor
     */
    public function getLogic()
    {
        return $this->logic;
    }

    /**
     * @param \XC\MigrationWizard\Logic\Import\Processor\AProcessor $logic
     */
    public function setLogic($logic)
    {
        $this->logic = $logic;
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
}
