<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\AttributeOption\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use XLite\Model\AttributeOption;
use XLite\Model\AttributeValue\AttributeValueSelect;

class PostWriteEventSubscriber implements EventSubscriberInterface
{
    protected SerializerContextBuilderInterface $serializerContextBuilder;

    protected EntityManagerInterface $entityManager;

    /**
     * @var string[]
     */
    protected array $optionNames = [
        'product_based_post_select',
    ];

    /**
     * @var string[]
     */
    protected array $entityTypes = [
        AttributeOption::class,
    ];

    public function __construct(
        SerializerContextBuilderInterface $serializerContextBuilder,
        EntityManagerInterface $entityManager
    ) {
        $this->serializerContextBuilder = $serializerContextBuilder;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['setOptionValue', EventPriorities::POST_WRITE],
        ];
    }

    public function setOptionValue(ViewEvent $event): void
    {
        $request = $event->getRequest();
        $attributes = RequestAttributesExtractor::extractAttributes($request);

        $context = $this->serializerContextBuilder->createFromRequest($request, false, $attributes);

        if (
            empty($context['collection_operation_name'])
            || !in_array($context['collection_operation_name'], $this->optionNames, true)
            || !in_array(get_class($event->getControllerResult()), $this->entityTypes)
        ) {
            return;
        }

        /** @var \XLite\Model\AttributeOption $attributeOption */
        $attributeOption = $event->getControllerResult();

        $product = $attributeOption->getAttribute()->getProduct();

        $attributeValue = new AttributeValueSelect();
        $attributeValue->setProduct($product);
        $attributeValue->setAttribute($attributeOption->getAttribute());
        $attributeValue->setAttributeOption($attributeOption);

        $this->entityManager->persist($attributeValue);
        $this->entityManager->flush();
    }
}
