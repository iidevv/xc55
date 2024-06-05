<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\ProductVariants\API\Endpoint\ProductVariant\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Exception\ItemNotFoundException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\Image\ImageInput;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantInput as InputDTO;
use XC\ProductVariants\API\Endpoint\ProductVariant\DTO\ProductVariantUpdate as UpdateDTO;
use XC\ProductVariants\Model\Image\ProductVariant\Image;
use XC\ProductVariants\Model\ProductVariant as Model;
use XC\ProductVariants\Model\Repo\ProductVariant as Repo;
use XLite\Core\Converter;
use XLite\Core\RemoteResource\RemoteResourceException;
use XLite\Core\RemoteResource\RemoteResourceFactory;
use XLite\Model\Product;
use XLite\Model\Repo\Product as ProductRepo;
use XLite\Model\AttributeValue\AttributeValueCheckbox;
use XLite\Model\AttributeValue\AttributeValueSelect;
use XLite\Model\Repo\AttributeValue\AttributeValueCheckbox as AttributeValueCheckboxRepo;
use XLite\Model\Repo\AttributeValue\AttributeValueSelect as AttributeValueSelectRepo;

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
            throw new InvalidArgumentException('Product ID is required');
        }

        $entity->setPrice($object->price);
        $entity->setDefaultPrice($object->default_price);
        $entity->setAmount($object->amount);
        $entity->setDefaultAmount($object->default_amount);
        $entity->setWeight($object->weight);
        $entity->setDefaultWeight($object->default_weight);
        $entity->setDefaultValue($object->default_variant);

        if ($object->sku) {
            /** @var Model $variant */
            $variant = $this->getProductVariantRepository()->findOneBySku($object->sku);
            if ($variant && (!$entity->isPersistent() || $entity->getId() != $variant->getId())) {
                throw new InvalidArgumentException('SKU must be unique');
            }
        }

        $entity->setSku($object->sku);

        $this->updateImage($entity, $object);

        if ($object instanceof InputDTO) {
            $this->checkAttributeValues($entity, $object, $productId);

            $this->updateAttributeCheckboxValues($entity, $object, $productId);
            $this->updateAttributeSelectValues($entity, $object, $productId);
        }

        return $entity;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Model) {
            return false;
        }

        return $to === Model::class
            && ($context['input']['class'] === InputDTO::class || $context['input']['class'] === UpdateDTO::class);
    }

    /**
     * @return InputDTO|UpdateDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Model $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$entity) {
            return new InputDTO();
        }

        $input = new UpdateDTO();
        $input->price = $entity->getPrice();
        $input->default_price = $entity->getDefaultPrice();
        $input->amount = $entity->getAmount();
        $input->default_amount = $entity->getDefaultAmount();
        $input->weight = $entity->getWeight();
        $input->default_weight = $entity->getDefaultWeight();
        $input->default_variant = $entity->getDefaultValue();
        $input->sku = $entity->getSku();
        $input->image = $this->assembleImage($entity->getImage());
        $input->attribute_checkbox_values = $this->assembleCheckboxValues($entity->getAttributeValueC());
        $input->attribute_select_values = $this->assembleSelectValues($entity->getAttributeValueS());

        return $input;
    }

    /**
     * @param Model              $entity
     * @param UpdateDTO|InputDTO $object
     *
     * @return void
     */
    protected function updateImage(Model $entity, $object): void
    {
        if ($entity->isPersistent() && !$object->image && $entity->getImage()) {
            $this->entityManager->remove($entity->getImage());
            return;
        }

        if (!$object->image) {
            return;
        }

        $file = $entity->getImage() ?: new Image();

        $file->setAlt($object->image->alt);

        if (!$file->isPersistent()) {
            $entity->setImage($file);
            $file->setProductVariant($entity);
        }

        if (!empty($object->image->externalUrl)) {
            try {
                RemoteResourceFactory::getRemoteResourceByURL($object->image->externalUrl);
            } catch (RemoteResourceException $e) {
                throw new InvalidArgumentException(sprintf('Cannot get image info from "%s"', $object->image->externalUrl));
            }

            $isSaved = $file->loadFromURL($object->image->externalUrl, true);
        } elseif (!empty($object->image->attachment) && !empty($object->image->filename)) {
            $tmp = LC_DIR_TMP . $object->image->filename;

            file_put_contents($tmp, base64_decode($object->image->attachment, true));

            $isSaved = $file->loadFromLocalFile(
                $tmp,
                pathinfo($tmp, \PATHINFO_FILENAME) . '.' . pathinfo($tmp, \PATHINFO_EXTENSION)
            );

            unlink($tmp);
        } elseif ($object instanceof UpdateDTO) {
            return;
        } else {
            throw new InvalidArgumentException("Fields 'image.attachment' and 'image.filename' or 'image.externalUrl' are required");
        }

        if (!$isSaved || $file->getSize() > Converter::getUploadFileMaxSize()) {
            if (!$isSaved) {
                $errorMessage = $file->getLoadErrorMessage()
                    ? json_encode($file->getLoadErrorMessage())
                    : 'Something went wrong';
            } else {
                $errorMessage = 'The image is too big';
            }

            unlink($file->getStoragePath());
            $this->entityManager->detach($file);

            throw new InvalidArgumentException($errorMessage);
        }
    }

    /**
     * @param Model    $entity
     * @param InputDTO $object
     *
     * @return void
     */
    protected function updateAttributeCheckboxValues(Model $entity, InputDTO $object, int $productId): void
    {
        $attributeIdList = [];
        foreach ($object->attribute_checkbox_values as $id) {
            /** @var AttributeValueCheckbox $value */
            $value = $this->getAttributeCheckboxValueRepository()->find($id);
            if ($value->getProduct()->getProductId() !== $productId) {
                throw new InvalidArgumentException(
                    sprintf('Checkbox attribute value #%d should be linked to product #%d', $id, $productId)
                );
            }

            if (in_array($value->getAttribute()->getId(), $attributeIdList, true)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Checkbox attribute value #%d should be linked to attribute #%d and this attribute is already in use in this variant',
                        $id,
                        $value->getAttribute()->getId()
                    )
                );
            }

            $entity->addAttributeValueC($value);
            $attributeIdList[] = $value->getAttribute()->getId();
        }
    }

    /**
     * @param Model    $entity
     * @param InputDTO $object
     *
     * @return void
     */
    protected function updateAttributeSelectValues(Model $entity, InputDTO $object, int $productId): void
    {
        $attributeIdList = [];
        foreach ($object->attribute_select_values as $id) {
            /** @var AttributeValueSelect $value */
            $value = $this->getAttributeSelectValueRepository()->find($id);
            if ($value->getProduct()->getProductId() !== $productId) {
                throw new InvalidArgumentException(
                    sprintf('Select box attribute value #%d should be linked to product #%d', $id, $productId)
                );
            }

            if (in_array($value->getAttribute()->getId(), $attributeIdList, true)) {
                throw new InvalidArgumentException(
                    sprintf(
                        'Select box attribute value #%d should be linked to attribute #%d and this attribute is already in use in this variant',
                        $id,
                        $value->getAttribute()->getId()
                    )
                );
            }

            $entity->addAttributeValueS($value);
            $attributeIdList[] = $value->getAttribute()->getId();
        }
    }

    protected function checkAttributeValues(Model $entity, InputDTO $object, int $productId): void
    {
        /** @var Product $product */
        $product = $this->getProductRepository()->find($productId);
        if ($product === null) {
            throw new ItemNotFoundException(sprintf('Product with ID %d not found', $productId));
        }

        $attrValues = [];
        foreach ($object->attribute_select_values as $id) {
            /** @var AttributeValueSelect $value */
            $value = $this->getAttributeSelectValueRepository()->find($id);
            if (!$value) {
                throw new InvalidArgumentException(sprintf('Select box attribute value with ID %d not found', $id));
            }

            $attrValues[] = $value;
        }

        foreach ($object->attribute_checkbox_values as $id) {
            /** @var AttributeValueCheckbox $value */
            $value = $this->getAttributeCheckboxValueRepository()->find($id);
            if (!$value) {
                throw new InvalidArgumentException(sprintf('Checkbox attribute value with ID %d not found', $id));
            }

            $attrValues[] = $value;
        }

        if (count($attrValues) === 0) {
            throw new InvalidArgumentException('Variant must have attribute values');
        }

        $ids = [];
        foreach ($attrValues as $av) {
            $ids[$av->getAttribute()->getId()] = $av->getId();
        }

        // Search for the same variant
        $sameVariants = $product->getVariantByAttributeValuesIds($ids, false);
        if (0 < count($sameVariants)) {
            throw new InvalidArgumentException('Variant with specified attribute values already exists');
        }
    }

    protected function getAttributeCheckboxValueRepository(): AttributeValueCheckboxRepo
    {
        return $this->entityManager->getRepository(AttributeValueCheckbox::class);
    }

    protected function getAttributeSelectValueRepository(): AttributeValueSelectRepo
    {
        return $this->entityManager->getRepository(AttributeValueSelect::class);
    }

    protected function getProductRepository(): ProductRepo
    {
        return $this->entityManager->getRepository(Product::class);
    }

    protected function getProductVariantRepository(): Repo
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

    protected function assembleImage(?Image $image): ?ImageInput
    {
        if (!$image) {
            return null;
        }

        $dto = new ImageInput();
        $dto->alt = $image->getAlt();

        return $dto;
    }

    /**
     * @param AttributeValueCheckbox[] $values
     *
     * @return int[]
     */
    protected function assembleCheckboxValues(Collection $values): array
    {
        return array_map(
            static function (AttributeValueCheckbox $value): int {
                return $value->getId();
            },
            $values->getValues()
        );
    }
    /**
     * @param AttributeValueSelect[] $values
     *
     * @return int[]
     */
    protected function assembleSelectValues(Collection $values): array
    {
        return array_map(
            static function (AttributeValueSelect $value): int {
                return $value->getId();
            },
            $values->getValues()
        );
    }
}
