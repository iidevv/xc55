<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\ProductImage\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XLite\API\Endpoint\ProductImage\DTO\ImageInput;
use XLite\Core\Converter;
use XLite\Model\Image\Product\Image;
use XLite\Model\Product;

class InputTransformer implements DataTransformerInterface, InputTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    protected ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validator
    ) {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @param ImageInput $object
     */
    public function transform($object, string $to, array $context = []): Image
    {
        $productId = $this->getProductId($context);
        if (!$productId) {
            throw new InvalidArgumentException('Product ID is invalid');
        }

        /** @var Product $product */
        $product = $this->entityManager->getRepository(Product::class)->find($productId);
        if (!$product) {
            throw new InvalidArgumentException(sprintf("Product with ID %s not found", $productId));
        }

        $image = new Image();

        if (!empty($object->externalUrl)) {
            $isSaved = $image->loadFromURL($object->externalUrl, true);
        } else {
            if (empty($object->attachment) || empty($object->filename)) {
                throw new InvalidArgumentException("Fields 'attachment' and 'filename' are required");
            }

            $tmp = LC_DIR_TMP . $object->filename;

            file_put_contents($tmp, base64_decode($object->attachment, true));

            $isSaved = $image->loadFromLocalFile(
                $tmp,
                pathinfo($tmp, \PATHINFO_FILENAME) . '.' . pathinfo($tmp, \PATHINFO_EXTENSION)
            );

            unlink($tmp);
        }

        if (!$isSaved || $image->getSize() > Converter::getUploadFileMaxSize()) {
            if (!$isSaved) {
                $errorMessage = $image->getLoadErrorMessage()
                    ? json_encode($image->getLoadErrorMessage())
                    : 'Something went wrong';
            } else {
                $errorMessage = 'The file is too big';
            }

            unlink($image->getStoragePath());
            $this->entityManager->detach($image);

            throw new InvalidArgumentException($errorMessage);
        }

        $image->setAlt($object->alt);
        $image->setOrderby($object->position);
        $image->setProduct($product);
        $product->addImages($image);

        return $image;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Image) {
            return false;
        }

        return $to === Image::class && ($context['input']['class'] ?? null) === ImageInput::class;
    }

    protected function getProductId(array $context): ?int
    {
        if (
            isset($context['request_uri'])
            && preg_match('/products\/(\d+)\/images/Ss', $context['request_uri'], $match)
        ) {
            return (int) $match[1];
        }

        return null;
    }
}
