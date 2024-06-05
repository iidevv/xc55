<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\Core\Model;

use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\PostFlushEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;
use XCart\Container;
use XCart\Messenger\Message\ResizeImage;
use XLite\Model\Image\Product\Image as ProductImage;
use XLite\Model\Image\Category\Image as CategoryIcon;

class ImageResizeScheduler implements EventSubscriberInterface
{
    protected static array $ids = [];

    protected ?Request $request;

    protected ?MessageBusInterface $bus;

    public function __construct(?RequestStack $requestStack)
    {
        $this->request = $requestStack ? $requestStack->getCurrentRequest() : null;
        $this->bus = Container::getContainer() ? Container::getContainer()->get('messenger.default_bus') : null;
    }

    public function getSubscribedEvents(): array
    {
        return [
            Events::postPersist,
            Events::preUpdate,
            Events::postFlush,
        ];
    }

    public function postPersist(LifecycleEventArgs $eventArgs)
    {
        /** @var ProductImage|CategoryIcon $image */
        $image = $eventArgs->getEntity();

        if (($image instanceof ProductImage || $image instanceof CategoryIcon) && $this->isApiRoute()) {
            $this->bus->dispatch(new ResizeImage($image->getId(), get_class($image)));
        }
    }

    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $instance = $eventArgs->getEntity();

        if ($instance instanceof CategoryIcon && $this->isApiRoute()) {
            $changeSet = $eventArgs->getEntityChangeSet();

            if (isset($changeSet['hash'])) {
                self::$ids[] = $instance->getId();
            }
        }
    }

    public function postFlush(PostFlushEventArgs $eventArgs)
    {
        if (!empty(static::$ids)) {
            foreach (static::$ids as $id) {
                $this->bus->dispatch(new ResizeImage($id, CategoryIcon::class));
            }

            static::$ids = [];
        }
    }

    protected function isApiRoute(): bool
    {
        return $this->request && substr($this->request->getRequestUri(), 0, 5) === '/api/';
    }
}
