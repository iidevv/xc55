<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActSkuVault\Core\Command\Push\Hydrator;

use XLite\Core\Database;

class Hydrator implements IObjectHydrator
{
    /**
     * @var string
     */
    protected $localId;

    /**
     * @var string
     */
    protected $entityClassName;

    /**
     * @var BaseConverter
     */
    protected $dtoClassObject;

    /**
     * Constructor
     *
     * @param string $localId
     * @param string $entityClassName
     * @param BaseConverter $dtoClassObject
     *
     * @return void
     */
    public function __construct(string $localId, string $entityClassName, BaseConverter $dtoClassObject)
    {
        $this->localId = $localId;
        $this->entityClassName = $entityClassName;
        $this->dtoClassObject = $dtoClassObject;
    }

    /**
     * Get DTO
     *
     * @return array
     * @throws HydratorException
     */
    public function getDTO(): array
    {
        /** @noinspection NullPointerExceptionInspection */
        $entity = Database::getRepo($this->entityClassName)->find($this->localId);

        if ($entity && $dto = $this->dtoClassObject->convert($entity)) {
            return $dto;
        }

        throw new HydratorException('DTO are not ready');
    }
}
