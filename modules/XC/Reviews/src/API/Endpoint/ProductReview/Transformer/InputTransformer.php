<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XC\Reviews\API\Endpoint\ProductReview\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use Doctrine\ORM\EntityManagerInterface;
use DateTimeImmutable;
use XC\Reviews\API\Endpoint\ProductReview\DTO\ProductReviewInput as InputDTO;
use XC\Reviews\Model\Review as Model;
use XLite\Model\Profile;
use XLite\Model\Repo\Profile as ProfileRepo;

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
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? new Model();

        $entity->setReview($object->review);
        $entity->setResponse($object->response);
        $entity->setRating($object->rating);
        $entity->setAdditionDate($object->addition_date ? $object->addition_date->getTimestamp() : time());
        $entity->setResponseDate($object->response_date ? $object->response_date->getTimestamp() : null);
        $entity->setReviewerName($object->reviewer_name);
        $entity->setStatus($object->status);
        $entity->setUseForMeta($object->use_for_meta);

        $respondent = null;
        if ($object->respondent) {
            $respondent = $this->getProfileRepository()->find($object->respondent);
            if (!$respondent) {
                throw new InvalidArgumentException(sprintf('Profile with ID %d not found', $object->respondent));
            }
        }
        $entity->setRespondent($respondent);

        $profile = null;
        if ($object->profile) {
            $profile = $this->getProfileRepository()->find($object->profile);
            if (!$profile) {
                throw new InvalidArgumentException(sprintf('Profile with ID %d not found', $object->profile));
            }
        }
        $entity->setProfile($profile);

        return $entity;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        if ($data instanceof Model) {
            return false;
        }

        return $to === Model::class && $context['input']['class'] === InputDTO::class;
    }

    /**
     * @return InputDTO
     */
    public function initialize(string $inputClass, array $context = [])
    {
        /** @var Model $entity */
        $entity = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE] ?? null;
        if (!$entity) {
            return new InputDTO();
        }

        $input = new InputDTO();
        $input->review = $entity->getReview();
        $input->response = $entity->getResponse();
        $input->rating = $entity->getRating();
        $input->addition_date = $entity->getAdditionDate()
            ? new DateTimeImmutable('@' . $entity->getAdditionDate())
            : null;
        $input->response_date = $entity->getResponseDate()
            ? new DateTimeImmutable('@' . $entity->getResponseDate())
            : null;
        $input->profile = $entity->getProfile() ? $entity->getProfile()->getProfileId() : null;
        $input->respondent = $entity->getRespondent() ? $entity->getRespondent()->getProfileId() : null;
        $input->reviewer_name = $entity->getReviewerName();
        $input->status = $entity->getStatus();
        $input->use_for_meta = $entity->getUseForMeta();

        return $input;
    }

    protected function getProfileRepository(): ProfileRepo
    {
        return $this->entityManager->getRepository(Profile::class);
    }
}
