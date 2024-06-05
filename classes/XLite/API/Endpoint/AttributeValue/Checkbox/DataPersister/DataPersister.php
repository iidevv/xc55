<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeValue\Checkbox\DataPersister;

use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\Model\AttributeValue\AttributeValueCheckbox;

class DataPersister implements ContextAwareDataPersisterInterface
{
    protected ContextAwareDataPersisterInterface $inner;

    protected EntityManagerInterface $entityManager;

    public function __construct(
        ContextAwareDataPersisterInterface $inner,
        EntityManagerInterface $entityManager
    ) {
        $this->inner = $inner;
        $this->entityManager = $entityManager;
    }

    public function supports($data, array $context = []): bool
    {
        return $this->inner->supports($data, $context)
            && $data instanceof AttributeValueCheckbox;
    }

    /**
     * @param AttributeValueCheckbox $data
     */
    public function persist($data, array $context = [])
    {
        $repository = $this->getAttributeValueCheckboxRepository();
        /** @var AttributeValueCheckbox $model */
        $model = $repository->findOneBy(
            [
                'product'   => $data->getProduct()->getId(),
                'value'     => $data->getValue(),
                'attribute' => $data->getAttribute()->getId(),
            ]
        );
        if ($model && $model->getId() !== $data->getId()) {
            throw new InvalidArgumentException('Attribute value with same value already exists');
        }

        return $this->inner->persist($data, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function remove($data, array $context = [])
    {
        $this->inner->remove($data, $context);
    }

    protected function getAttributeValueCheckboxRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(AttributeValueCheckbox::class);
    }
}
