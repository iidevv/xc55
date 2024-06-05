<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\CategoryIcon\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XLite\API\Endpoint\CategoryIcon\DTO\IconInput;
use XLite\Core\Converter;
use XLite\Model\Category;
use XLite\Model\Image\Category\Image;

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
     * @param IconInput $object
     */
    public function transform($object, string $to, array $context = []): Image
    {
        $categoryId = $this->getCategoryId($context);
        if (!$categoryId) {
            throw new InvalidArgumentException('Category ID is invalid');
        }

        /** @var Category $category */
        $category = $this->entityManager->getRepository(Category::class)->find($categoryId);
        if (!$category) {
            throw new InvalidArgumentException(sprintf("Category with ID %s not found", $categoryId));
        }

        $image = $category->getImage();

        if (!$image) {
            $image = new Image();
        }

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
        $image->setCategory($category);
        $category->setImage($image);

        return $image;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Image) {
            return false;
        }

        return $to === Image::class && ($context['input']['class'] ?? null) === IconInput::class;
    }

    protected function getCategoryId(array $context): ?int
    {
        if (
            isset($context['request_uri'])
            && preg_match('/categories\/(\d+)\/icon/Ss', $context['request_uri'], $match)
        ) {
            return (int) $match[1];
        }

        return null;
    }
}
