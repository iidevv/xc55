<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Migration Rule Translation
 *
 * @ORM\Entity
 * @ORM\Table (name="migration_wizard_rule_translations",
 *         indexes={
 *              @ORM\Index (name="ci", columns={"code","id"}),
 *              @ORM\Index (name="id", columns={"id"}),
 *              @ORM\Index (name="name", columns={"name"})
 *         }
 * )
 */
class MigrationRuleTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Rule name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * @var \XC\MigrationWizard\Model\MigrationRule
     *
     * @ORM\ManyToOne (targetEntity="XC\MigrationWizard\Model\MigrationRule", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="ruleId", onDelete="CASCADE")
     */
    protected $owner;

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
