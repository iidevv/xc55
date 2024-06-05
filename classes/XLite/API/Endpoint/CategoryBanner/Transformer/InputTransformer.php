<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\CategoryBanner\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use XLite\API\Endpoint\CategoryBanner\DTO\BannerInput;
use XLite\Core\Converter;
use XLite\Model\Category;
use XLite\Model\Image\Category\Banner;

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
     * @param BannerInput $object
     */
    public function transform($object, string $to, array $context = []): Banner
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

        $banner = $category->getBanner();

        if (!$banner) {
            $banner = new Banner();
        }

        if (!empty($object->externalUrl)) {
            $isSaved = $banner->loadFromURL($object->externalUrl, true);
        } else {
            if (empty($object->attachment) || empty($object->filename)) {
                throw new InvalidArgumentException("Fields 'attachment' and 'filename' are required");
            }

            $tmp = LC_DIR_TMP . $object->filename;

            file_put_contents($tmp, base64_decode($object->attachment, true));

            $isSaved = $banner->loadFromLocalFile(
                $tmp,
                pathinfo($tmp, \PATHINFO_FILENAME) . '.' . pathinfo($tmp, \PATHINFO_EXTENSION)
            );

            unlink($tmp);
        }

        if (!$isSaved || $banner->getSize() > Converter::getUploadFileMaxSize()) {
            if (!$isSaved) {
                $errorMessage = $banner->getLoadErrorMessage()
                    ? json_encode($banner->getLoadErrorMessage())
                    : 'Something went wrong';
            } else {
                $errorMessage = 'The file is too big';
            }

            unlink($banner->getStoragePath());
            $this->entityManager->detach($banner);

            throw new InvalidArgumentException($errorMessage);
        }

        $banner->setAlt($object->alt);
        $banner->setCategory($category);
        $category->setBanner($banner);

        return $banner;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Banner) {
            return false;
        }

        return $to === Banner::class && ($context['input']['class'] ?? null) === BannerInput::class;
    }

    protected function getCategoryId(array $context): ?int
    {
        if (
            isset($context['request_uri'])
            && preg_match('/categories\/(\d+)\/banner/Ss', $context['request_uri'], $match)
        ) {
            return (int) $match[1];
        }

        return null;
    }
}
