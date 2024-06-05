<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\LoyaltyProgram\API\Endpoint\Order\Transformer\RewardHistoryEvent;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use DateTimeImmutable;
use QSL\LoyaltyProgram\API\Endpoint\Order\DTO\RewardHistoryEvent\OrderRewardHistoryEventOutput as OutputDTO;
use QSL\LoyaltyProgram\Model\RewardHistoryEvent;

class OutputTransformer implements DataTransformerInterface, OutputTransformerInterface
{
    /**
     * @param RewardHistoryEvent $object
     */
    public function transform($object, string $to, array $context = []): OutputDTO
    {
        $dto = new OutputDTO();
        $dto->id = $object->getEventId();
        $dto->date = new DateTimeImmutable('@' . $object->getDate());
        $dto->points = $object->getPoints();
        $dto->reason = $object->getReason();
        $dto->comment = $object->getComment();
        $dto->user_id = $object->getUser() ? $object->getUser()->getProfileId() : null;
        $dto->initiator_id = $object->getInitiator() ? $object->getInitiator()->getProfileId() : null;

        return $dto;
    }

    public function supportsTransformation($data, string $to, array $context = []): bool
    {
        return $to === OutputDTO::class && $data instanceof RewardHistoryEvent;
    }
}
