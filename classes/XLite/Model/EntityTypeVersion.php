<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Entity type version is a UUID that changes every time an entity of the given type is persisted/updated/removed
 *
 * @ORM\Entity (repositoryClass="XLite\Model\Repo\EntityTypeVersion")
 * @ORM\Table (name="entity_type_versions")
 */
class EntityTypeVersion
{
    /**
     * Primary key
     *
     * @var integer
     *
     * @ORM\Id
     * @ORM\GeneratedValue (strategy="AUTO")
     * @ORM\Column         (type="integer")
     */
    protected $id;

    /**
     * Entity FQCN
     *
     * @var string
     *
     * @ORM\Column (type="string", length=255, unique=true)
     */
    protected $entityType;

    /**
     * Entity type version
     *
     * @var string
     *
     * @ORM\Column (type="guid")
     */
    protected $version;

    public function __construct($entityType, $version)
    {
        $this->entityType = $entityType;
        $this->version    = $version;
    }
}
