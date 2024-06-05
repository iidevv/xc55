<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Product\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XLite\API\Endpoint\Product\DTO\Input as InputDTO;
use XLite\API\Language;
use XLite\Core\Converter;
use XLite\Model\CleanURL;
use XLite\Model\Membership;
use XLite\Model\Product;
use XLite\Model\ProductClass;
use XLite\Model\TaxClass;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    protected ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->validator     = $validator;
    }

    /**
     * @param InputDTO $object
     */
    public function transform($object, string $to, array $context = []): Product
    {
        $violations = $this->validator->validate($object);
        if (count($violations) > 0) {
            throw new InvalidArgumentException(sprintf("Input validations failed: %s", $violations));
        }

        $model = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Product();

        $this->validate($model, $object);

        if (Language::getInstance()->getLanguageCode()) {
            $model->setEditLanguage(Language::getInstance()->getLanguageCode());
        }
        $model->setSku($object->sku);
        $model->setName($object->name);
        $model->setDescription($object->description);
        $model->setBriefDescription($object->brief_description);
        $model->setMetaTags($object->meta_tags);
        $model->setMetaDescType($object->meta_description_type);
        $model->setMetaDesc($object->meta_description);
        $model->setMetaTitle($object->meta_title);
        $model->setPrice($object->price);
        $model->setEnabled($object->enabled);
        $model->setWeight($object->weight);
        $model->setUseSeparateBox($object->separate_box);
        $model->setBoxWidth($object->width);
        $model->setBoxLength($object->length);
        $model->setBoxHeight($object->height);
        $model->setFreeShipping($object->free_shipping);
        $model->setTaxable($object->taxable);

        $arrivalDate = new \DateTime('now', Converter::getTimeZone());
        $arrivalDate->setTimestamp($object->arrival_date ? strtotime($object->arrival_date) : time());
        $arrivalDate->modify('midnight');
        $model->setArrivalDate($arrivalDate->getTimestamp());

        $model->setInventoryEnabled($object->inventory_traceable);
        $model->setAmount($object->amount);

        $productClassRepo = $this->entityManager->getRepository(ProductClass::class);
        $productClass     = $object->product_class ? $productClassRepo->findOneByName($object->product_class) : null;
        $model->setProductClass($productClass);

        $taxClassRepo = $this->entityManager->getRepository(TaxClass::class);
        $taxClass     = $object->tax_class ? $taxClassRepo->findOneByName($object->tax_class) : null;
        $model->setTaxClass($taxClass);

        $membershipRepo = $this->entityManager->getRepository(Membership::class);
        $memberships    = $membershipRepo->findByNames($object->memberships);
        $model->replaceMembershipsByMemberships($memberships);

        if (!empty($object->clean_url)) {
            $model->setCleanURL($object->clean_url);
        }

        return $model;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Product) {
            return false;
        }

        return $to === Product::class && ($context['input']['class'] ?? null) === InputDTO::class;
    }

    /**
     * @return InputDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Product $product */
        $product = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;

        if (!$product) {
            return new InputDTO();
        }

        $input                        = new InputDTO();
        $input->sku                   = $product->getSku();
        $input->name                  = $product->getName();
        $input->description           = $product->getDescription();
        $input->brief_description     = $product->getBriefDescription();
        $input->meta_tags             = $product->getMetaTags();
        $input->meta_description_type = $product->getMetaDescType();
        $input->meta_description      = $product->getMetaDesc();
        $input->meta_title            = $product->getMetaTitle();
        $input->price                 = $product->getPrice();
        $input->enabled               = $product->getEnabled();
        $input->weight                = $product->getWeight();
        $input->separate_box          = $product->getUseSeparateBox();
        $input->width                 = $product->getBoxWidth();
        $input->length                = $product->getBoxLength();
        $input->height                = $product->getBoxHeight();
        $input->free_shipping         = $product->getFreeShipping();
        $input->taxable               = $product->getTaxable();
        $input->arrival_date          = date('c', $product->getArrivalDate());
        $input->inventory_traceable   = $product->getInventoryEnabled();
        $input->amount                = $product->getAmount();
        $input->product_class         = $product->getProductClass() ? $product->getProductClass()->getName() : null;
        $input->tax_class             = $product->getTaxClass() ? $product->getTaxClass()->getName() : null;
        $input->memberships           = $product->getMemberships()->map(static fn($m) => $m->getName())->toArray();
        $input->clean_url             = $product->getCleanURL();

        return $input;
    }

    protected function validate(Product $model, InputDTO $object): void
    {
        /** @var \XLite\Model\Repo\Product $productRepo */
        $productRepo = $this->entityManager->getRepository(Product::class);

        $entity = $productRepo->findOneBySku($object->sku);
        if (
            $entity
            && (is_null($model->getProductId())
                || $entity->getProductId() !== $model->getProductId()
            )
        ) {
            throw new InvalidArgumentException(
                sprintf("Input validations failed: %s", "SKU {$object->sku} is not unique")
            );
        }

        /** @var \XLite\Model\Repo\CleanURL $repo */
        $cleanUrlRepo = $this->entityManager->getRepository(CleanURL::class);

        if (!empty($object->clean_url) && !$cleanUrlRepo->isURLUnique($object->clean_url, $model)) {
            throw new InvalidArgumentException(
                sprintf("Input validations failed: %s", "Clean URL {$object->clean_url} is not unique")
            );
        }
    }
}
