<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Profile\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInitializerInterface;
use ApiPlatform\Core\Exception\InvalidArgumentException;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use XLite\API\Endpoint\Profile\DTO\ProfileInput as InputDTO;
use XLite\Core\Auth;
use XLite\Model\Membership;
use XLite\Model\Profile as Model;
use XLite\Model\Role;

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
        $entity->setLogin($object->login);
        if (!is_null($object->password)) {
            $entity->setPassword(Auth::encryptPassword($object->password));
        }
        $entity->setAccessLevel($object->access_level);
        $entity->setStatus($object->status);
        $entity->setStatusComment($object->status_comment);
        $entity->setReferer($object->referer);
        $entity->setLanguage($object->language);
        $entity->setForceChangePassword($object->force_change_password);

        $membership = null;
        if ($object->membership_id) {
            $membership = $this->getMembershipRepository()->find($object->membership_id);
            if (!$membership) {
                throw new InvalidArgumentException(sprintf('Membership with ID %d not found', $object->membership_id));
            }
        }
        $entity->setMembership($membership);

        $pending_membership = null;
        if ($object->pending_membership_id) {
            $pending_membership = $this->getMembershipRepository()->find($object->pending_membership_id);
            if (!$pending_membership) {
                throw new InvalidArgumentException(sprintf('Pending membership with ID %d not found', $object->pending_membership_id));
            }
        }
        $entity->setPendingMembership($pending_membership);

        if ($entity->isAdmin()) {
            $this->updateRoles($entity, $object->role_ids);
        }

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
        $input->login = $entity->getLogin();
        $input->access_level = $entity->getAccessLevel();
        $input->create_date = new DateTimeImmutable('@' . $entity->getAdded());
        $input->first_login_date = $entity->getFirstLogin() ? new DateTimeImmutable('@' . $entity->getFirstLogin()) : null;
        $input->last_login_date = $entity->getLastLogin() ? new DateTimeImmutable('@' . $entity->getLastLogin()) : null;
        $input->status = $entity->getStatus();
        $input->status_comment = $entity->getStatusComment();
        $input->referer = $entity->getReferer();
        $input->language = $entity->getLanguage();
        $input->membership_id = $entity->getMembershipId();
        $input->pending_membership_id = $entity->getPendingMembershipId();
        $input->force_change_password = $entity->getForceChangePassword();

        $input->role_ids = [];

        /** @var Role $role */
        foreach ($entity->getRoles() as $role) {
            $input->role_ids[] = $role->getId();
        }

        return $input;
    }

    protected function getMembershipRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Membership::class);
    }

    protected function getRoleRepository(): EntityRepository
    {
        return $this->entityManager->getRepository(Role::class);
    }

    public function updateRoles(Model $entity, array $idList): void
    {
        $collection = $entity->getRoles();

        foreach ($idList as $id) {
            $found = false;
            /** @var Role $subEntity */
            foreach ($collection as $subEntity) {
                $subEntityId = $subEntity->getId();
                if ($subEntityId === $id) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // Add
                $subEntity = $this->getRoleRepository()->find($id);
                if (!$subEntity) {
                    throw new InvalidArgumentException(sprintf('Role with ID %d not found', $id));
                }

                $collection->add($subEntity);
                $subEntity->addProfiles($entity);
            }
        }

        /** @var Role $subEntity */
        foreach ($collection as $subEntity) {
            $found = false;
            foreach ($idList as $id) {
                $subEntityId = $subEntity->getId();
                if ($subEntityId === $id) {
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                // Remove
                $collection->removeElement($subEntity);
                $subEntity->getProfiles()->removeElement($entity);
            }
        }
    }
}
