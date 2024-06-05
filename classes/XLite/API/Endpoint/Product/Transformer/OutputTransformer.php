<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Product\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use XLite\API\Endpoint\Membership\DTO\MembershipOutput;
use XLite\API\Endpoint\Product\DTO\Output as ProductOutput;
use XLite\API\Endpoint\ProductClass\DTO\ProductClassOutput;
use XLite\API\Endpoint\ProductImage\DTO\ImageOutput;
use XLite\API\Endpoint\TaxClass\DTO\TaxClassOutput;
use XLite\Model\Image\Product\Image;
use XLite\Model\Membership;
use XLite\Model\Product;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Product $object
     */
    public function transform($object, string $to, array $context = []): ProductOutput
    {
        $output = new ProductOutput();
        $output->id = $object->getProductId();
        $output->sku = $object->getSku();
        $output->name = $object->getName();
        $output->description = $object->getDescription();
        $output->brief_description = $object->getBriefDescription();
        $output->meta_tags = $object->getMetaTags();
        $output->meta_description = $object->getMetaDesc();
        $output->meta_title = $object->getMetaTitle();
        $output->price = $object->getPrice();
        $output->enabled = $object->getEnabled();
        $output->weight = $object->getWeight();
        $output->separate_box = $object->getUseSeparateBox();
        $output->width = $object->getBoxWidth();
        $output->length = $object->getBoxLength();
        $output->height = $object->getBoxHeight();
        $output->free_shipping = $object->getFreeShipping();
        $output->taxable = $object->getTaxable();
        $output->create_date = date('c', $object->getDate());
        $output->update_date = date('c', $object->getUpdateDate());
        $output->arrival_date = date('c', $object->getArrivalDate());
        $output->inventory_traceable = $object->getInventoryEnabled();
        $output->amount = $object->getAmount();
        $output->product_class = $this->getProductClass($object);
        $output->tax_class = $this->getTaxClass($object);
        $output->memberships = $this->getMemberships($object);
        $output->clean_url = $object->getCleanURL();
        $output->images = $this->getImages($object);

        return $output;
    }

    public function getProductClass(Product $object): ?ProductClassOutput
    {
        $productClass = $object->getProductClass();

        if (!$productClass) {
            return null;
        }

        $output = new ProductClassOutput();
        $output->id = $productClass->getId();
        $output->name = $productClass->getName();

        return $output;
    }

    public function getTaxClass(Product $object): ?TaxClassOutput
    {
        $taxClass = $object->getTaxClass();

        if (!$taxClass) {
            return null;
        }

        $output = new TaxClassOutput();
        $output->id = $taxClass->getId();
        $output->name = $taxClass->getName();

        return $output;
    }

    /**
     * @return MembershipOutput[]
     */
    public function getMemberships(Product $object): array
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
     * @return ImageOutput[]
     */
    public function getImages(Product $object): array
    {
        $images = [];

        /** @var Image $image */
        foreach ($object->getImages() as $image) {
            $output = new ImageOutput();
            $output->id = $image->getId();
            $output->position = $image->getOrderby();
            $output->alt = $image->getAlt();
            $output->url = $image->getFrontURL();
            $output->width = $image->getWidth();
            $output->height = $image->getHeight();
            $images[] = $output;
        }

        return $images;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === ProductOutput::class && $data instanceof Product;
    }
}
