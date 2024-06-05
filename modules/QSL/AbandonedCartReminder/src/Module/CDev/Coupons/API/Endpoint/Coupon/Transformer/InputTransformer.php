<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\AbandonedCartReminder\Module\CDev\Coupons\API\Endpoint\Coupon\Transformer;

use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use CDev\Coupons\API\Endpoint\Coupon\Transformer\InputTransformer as ParentInputTransformer;
use Doctrine\ORM\EntityManagerInterface;
use QSL\AbandonedCartReminder\Module\CDev\Coupons\API\Endpoint\Coupon\DTO\CouponInput as InputDTO;
use CDev\Coupons\Model\Coupon as Model;
use QSL\AbandonedCartReminder\Module\CDev\Coupons\Model\Coupon as CurrentModel;
use XLite\Model\Order;
use XCart\Extender\Mapping\Extender;

/**
 * @Extender\Mixin
 * @Extender\Depend ("CDev\Coupons")
 */
class InputTransformer extends ParentInputTransformer
{
    protected EntityManagerInterface $entityManager;

    /**
     * @required
     */
    public function setEntityManager(EntityManagerInterface $entityManager): void
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        /** @var CurrentModel $entity */
        $entity = parent::transform($object, $to, $context);

        $abandoned_cart = null;
        if ($object->abandoned_cart) {
            $abandoned_cart = $this->entityManager->getRepository(Order::class)->find($object->abandoned_cart);
            if (!$abandoned_cart) {
                throw new InvalidArgumentException(sprintf('Abandoned cart with ID %d not found', $object->abandoned_cart));
            }
        }
        $entity->setAbandonedCart($abandoned_cart);
        $entity->setAbandonedCartCoupon($object->abandoned_cart_coupon);

        return $entity;
    }

    /**
     * @return InputDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var InputDTO $dto */
        $dto = parent::initialize($inputClass, $context);

        /** @var CurrentModel $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$entity) {
            return $dto;
        }

        $dto->abandoned_cart = $entity->getAbandonedCart() ? $entity->getAbandonedCart()->getOrderId() : null;
        $dto->abandoned_cart_coupon = $entity->isAbandonedCartCoupon();

        return $dto;
    }
}
