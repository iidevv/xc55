<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\MigrationWizard\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Migration Category Translation
 *
 * @ORM\Entity
 * @ORM\Table (name="migration_wizard_category_translations",
 *         indexes={
 *              @ORM\Index (name="ci", columns={"code","id"}),
 *              @ORM\Index (name="id", columns={"id"}),
 *              @ORM\Index (name="name", columns={"name"})
 *         }
 * )
 */
class MigrationCategoryTranslation extends \XLite\Model\Base\Translation
{
    /**
     * Category name
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255)
     */
    protected $name;

    /**
     * @var \XC\MigrationWizard\Model\MigrationCategory
     *
     * @ORM\ManyToOne (targetEntity="XC\MigrationWizard\Model\MigrationCategory", inversedBy="translations")
     * @ORM\JoinColumn (name="id", referencedColumnName="categoryId", onDelete="CASCADE")
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
