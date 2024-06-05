<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\Wholesale\API\Endpoint\ProductWholesalePrice\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use CDev\Wholesale\Model\Repo\WholesalePrice as WholesalePriceRepo;
use Doctrine\ORM\EntityManagerInterface;
use CDev\Wholesale\API\Endpoint\ProductWholesalePrice\DTO\ProductWholesalePriceInput as InputDTO;
use CDev\Wholesale\Model\WholesalePrice as Model;
use XLite\Model\Membership;
use XLite\Model\Repo\Membership as MembershipRepo;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        /** @var Model $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();

        $productId = $this->detectProductId($context);
        if (!$productId) {
            throw new InvalidArgumentException('Product ID is invalid');
        }

        $entity->setType($object->type);
        $entity->setPrice($object->price);

        $this->checkUniqueness($entity, $object, $productId);

        $entity->setQuantityRangeBegin($object->quantity_range_begin);

        $membership = null;
        if ($object->membership) {
            $membership = $this->getMembershipRepository()->find($object->membership);
            if (!$membership) {
                throw new InvalidArgumentException(sprintf('Membership with ID %d not found', $object->membership));
            }
        }
        $entity->setMembership($membership);

        return $entity;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Model) {
            return false;
        }

        return $to === Model::class && $context['input']['class'] === InputDTO::class;
    }

    /**
     * @return InputDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Model $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$entity) {
            return new InputDTO();
        }

        $input = new InputDTO();
        $input->type = $entity->getType();
        $input->price = $entity->getPrice();
        $input->quantity_range_begin = $entity->getQuantityRangeBegin();
        $input->membership = $entity->getMembership() ? $entity->getMembership()->getMembershipId() : null;

        return $input;
    }

    protected function getMembershipRepository(): MembershipRepo
    {
        return $this->entityManager->getRepository(Membership::class);
    }

    protected function getWholesalePriceRepository(): WholesalePriceRepo
    {
        return $this->entityManager->getRepository(Model::class);
    }

    protected function detectProductId(array $context): ?int
    {
        if (preg_match('/products\/(\d+)\//S', $context['request_uri'], $match)) {
            return (int) $match[1];
        }

        return null;
    }

    protected function checkUniqueness(Model $entity, InputDTO $object, int $productId): void
    {
        if (!$this->needCheckUniqueness($entity, $object)) {
            return;
        }

        $conditions = [
            'product'            => $productId,
            'quantityRangeBegin' => $object->quantity_range_begin,
            'membership'         => $object->membership,
        ];
        if ($this->getWholesalePriceRepository()->countBy($conditions) > 0) {
            throw new InvalidArgumentException('Combination of "Quantity range begin" and "Membership" fields must be unique');
        }
    }

    protected function needCheckUniqueness(Model $entity, InputDTO $object): bool
    {
        $entityMembershipId = $entity->getMembership() ? $entity->getMembership()->getMembershipId() : null;
        $objectMembershipId = $object->membership;

        return !$entity->isPersistent()
            || (
                $entity->getQuantityRangeBegin() != $object->quantity_range_begin
                || $entityMembershipId !== $objectMembershipId
            );
    }
}
