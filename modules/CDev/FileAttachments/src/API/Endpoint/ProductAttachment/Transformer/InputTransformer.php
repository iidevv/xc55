<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace CDev\FileAttachments\API\Endpoint\ProductAttachment\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use CDev\FileAttachments\API\Endpoint\ProductAttachment\DTO\ProductAttachmentInput as InputDTO;
use CDev\FileAttachments\API\Endpoint\ProductAttachment\DTO\ProductAttachmentUpdateInput as UpdateDTO;
use CDev\FileAttachments\Model\Product\Attachment as Model;
use CDev\FileAttachments\Model\Product\Attachment\Storage as File;
use Includes\Utils\FileManager;
use XLite\Core\Converter;
use XLite\Core\RemoteResource\RemoteResourceException;
use XLite\Core\RemoteResource\RemoteResourceFactory;
use XLite\Model\Membership;
use XLite\Model\Product;
use XLite\Model\Repo\Membership as MembershipRepo;
use XLite\Model\Repo\Product as ProductRepo;

class InputTransformer implements DataTransformerInitializerInterface, InputTransformerInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(
        EntityManagerInterface $entityManager
    ) {
        $this->entityManager = $entityManager;
    }

    /**
     * @param InputDTO|UpdateDTO $object
     */
    public function transform($object, string $to, array $context = []): Model
    {
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();

        $entity->setTitle($object->title);
        $entity->setDescription($object->description);
        $entity->setOrderby($object->position);

        if (preg_match('/^\d+$/Ss', $object->access)) {
            $membershipId = (int)$object->access;
            $membership = $this->getMembershipRepository()->find($membershipId);
            if (!$membership) {
                throw new InvalidArgumentException(sprintf('Membership with ID %d not found', $membershipId));
            }

            $entity->setAccess($membership);
        } else {
            $entity->setAccess($object->access);
        }

        if ($object instanceof InputDTO) {
            $productId = $this->detectProductId($context['request_uri']);
            if (!$productId) {
                throw new InvalidArgumentException('Product ID is invalid');
            }
            $product = $this->getProductRepository()->find($productId);
            if (!$product) {
                throw new InvalidArgumentException(sprintf('Product with ID %d not found', $productId));
            }

            $entity->setProduct($product);

            $this->uploadFile(
                $object->external_url,
                $object->attachment,
                $object->filename,
                !$entity->isPersistent(),
                $entity->isPersistent() ? $entity->getStorage() : null,
                $entity
            );
        }

        return $entity;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Model) {
            return false;
        }

        return $to === Model::class && ($context['input']['class'] === InputDTO::class || $context['input']['class'] === UpdateDTO::class);
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
        $input->title = $entity->getTitle();
        $input->description = $entity->getDescription();
        $input->position = $entity->getOrderby();
        $input->access = $entity->getAccess();

        return $input;
    }

    protected function uploadFile(?string $externalUrl, ?string $attachment, ?string $filename, bool $required, ?File $file, Model $entity): ?File
    {
        $file = $file ?: new File();

        $entity->setStorage($file);
        $file->setAttachment($entity);

        if (!empty($externalUrl)) {
            try {
                $info = RemoteResourceFactory::getRemoteResourceByURL($externalUrl);
            } catch (RemoteResourceException $e) {
                throw new InvalidArgumentException(sprintf('Cannot get file info from "%s"', $externalUrl));
            }

            $filename = $info->getName();
            foreach ($entity->getAttachments() as $attach) {
                if ($attach->getFileName() === $filename) {
                    throw new InvalidArgumentException('The same file can not be assigned to one product');
                }
            }

            $isSaved = $file->loadFromURL($externalUrl, true);
        } elseif (!empty($attachment) && !empty($filename)) {
            foreach ($entity->getAttachments() as $attach) {
                if ($attach->getFileName() === $filename) {
                    throw new InvalidArgumentException('The same file can not be assigned to one product');
                }
            }

            $tmp = LC_DIR_TMP . $filename;

            file_put_contents($tmp, base64_decode($attachment, true));

            $isSaved = $file->loadFromLocalFile(
                $tmp,
                pathinfo($tmp, \PATHINFO_FILENAME) . '.' . pathinfo($tmp, \PATHINFO_EXTENSION)
            );

            unlink($tmp);
        } elseif (!$required) {
            return null;
        } else {
            throw new InvalidArgumentException("Fields 'attachment' and 'filename' or 'external_url' are required");
        }

        if (!$isSaved || $file->getSize() > Converter::getUploadFileMaxSize()) {
            if (!$isSaved) {
                $errorMessage = $file->getLoadErrorMessage()
                    ? json_encode($file->getLoadErrorMessage())
                    : 'Something went wrong';
            } else {
                $errorMessage = 'The file is too big';
            }

            unlink($file->getStoragePath());
            $this->entityManager->detach($file);

            throw new InvalidArgumentException($errorMessage);
        }

        $hash = $this->getAttachmentHash($file);
        /** @var Model $attach */
        foreach ($entity->getProduct()->getAttachments() as $attach) {
            if ($attach->getId() !== $entity->getId()) {
                if ($this->getAttachmentHash($attach->getStorage()) === $hash) {
                    throw new InvalidArgumentException('The same file can not be assigned to one product');
                }
            }
        }


        return $file;
    }

    protected function detectProductId(string $uri): ?int
    {
        if (preg_match('/\/products\/(\d+)\/attachments/Ss', $uri, $match)) {
            return (int)$match[1];
        }

        return null;
    }

    protected function getProductRepository(): ProductRepo
    {
        return $this->entityManager->getRepository(Product::class);
    }

    protected function getMembershipRepository(): MembershipRepo
    {
        return $this->entityManager->getRepository(Membership::class);
    }

    protected function getAttachmentHash(File $storage): string
    {
        return FileManager::getHash($storage->getStoragePath());
    }
}
