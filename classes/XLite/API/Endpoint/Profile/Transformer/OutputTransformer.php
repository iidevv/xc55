<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XLite\API\Endpoint\Profile\Transformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use DateTimeImmutable;
use Exception;
use XLite\API\Endpoint\Profile\DTO\ProfileOutput as OutputDTO;
use XLite\Model\Profile;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param Profile $object
     *
     * @throws Exception
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getProfileId();
        $dto->login = $object->getLogin();
        $dto->access_level = $object->getAccessLevel();
        $dto->create_date = new DateTimeImmutable('@' . $object->getAdded());
        $dto->first_login_date = $object->getFirstLogin() ? new DateTimeImmutable('@' . $object->getFirstLogin()) : null;
        $dto->last_login_date = $object->getLastLogin() ? new DateTimeImmutable('@' . $object->getLastLogin()) : null;
        $dto->status = $object->getStatus();
        $dto->status_comment = $object->getStatusComment();
        $dto->referer = $object->getReferer();
        $dto->language = $object->getLanguage();
        $dto->membership_id = $object->getMembershipId();
        $dto->pending_membership_id = $object->getPendingMembershipId();
        $dto->force_change_password = $object->getForceChangePassword();
        $dto->role_ids = [];

        /** @var \XLite\Model\Role $role */
        foreach ($object->getRoles() as $role) {
            $dto->role_ids[] = $role->getId();
        }

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof Profile;
    }
}
