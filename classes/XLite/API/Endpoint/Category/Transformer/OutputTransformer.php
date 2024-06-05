<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Category\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use Doctrine\ORM\EntityManagerInterface;
use XLite\API\Endpoint\Category\DTO\Output as CategoryOutput;
use XLite\API\Endpoint\CategoryBanner\DTO\BannerOutput;
use XLite\API\Endpoint\CategoryIcon\DTO\IconOutput;
use XLite\API\Endpoint\Membership\DTO\MembershipOutput;
use XLite\Model\Category;
use XLite\Model\Membership;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param Category $object
     */
    public function transform($object, string $to, array $context = []): CategoryOutput
    {
        /** @var \XLite\Model\Repo\Category $categoryRepo */
        $categoryRepo = $this->entityManager->getRepository(Category::class);

        $output = new CategoryOutput();
        $output->id = $object->getCategoryId();
        $output->show_title = $object->getShowTitle();
        $output->position = $object->getPosition();
        $output->parent = $object->getParentId() === $categoryRepo->getRootCategoryId() ? null : $object->getParentId();
        $output->name = $object->getName();
        $output->description = $object->getDescription();
        $output->meta_tags = $object->getMetaTags();
        $output->meta_description = $object->getMetaDesc();
        $output->meta_title = $object->getMetaTitle();
        $output->enabled = $object->getEnabled();
        $output->memberships = $this->getMemberships($object);
        $output->clean_url = $object->getCleanURL();
        $output->icon = $this->getIcon($object);
        $output->banner = $this->getBanner($object);

        return $output;
    }

    /**
     * @return MembershipOutput[]
     */
    public function getMemberships(Category $object): array
    {
        $memberships = [];

        /** @var Membership $membership */
        foreach ($object->getMemberships() as $membership) {
            $output = new MembershipOutput();
            $output->id = $membership->getMembershipId();
            $output->name = $membership->getName();
            $output->enabled = $membership->getEnabled();
            $memberships[] = $output;
        }

        return $memberships;
    }

    /**
     * @param Category $object
     *
     * @return IconOutput|null
     */
    public function getIcon(Category $object): ?IconOutput
    {
        if (!$object->getImage()) {
            return null;
        }

        $icon = $object->getImage();

        $output = new IconOutput();
        $output->alt = $icon->getAlt();
        $output->url = $icon->getFrontURL();
        $output->width = $icon->getWidth();
        $output->height = $icon->getHeight();

        return $output;
    }

    /**
     * @param Category $object
     *
     * @return BannerOutput|null
     */
    public function getBanner(Category $object): ?BannerOutput
    {
        if (!$object->getBanner()) {
            return null;
        }

        $banner = $object->getBanner();

        $output = new BannerOutput();
        $output->alt = $banner->getAlt();
        $output->url = $banner->getFrontURL();
        $output->width = $banner->getWidth();
        $output->height = $banner->getHeight();

        return $output;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === CategoryOutput::class && $data instanceof Category;
    }
}
