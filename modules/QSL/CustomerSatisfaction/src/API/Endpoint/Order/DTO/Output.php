<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace QSL\CustomerSatisfaction\API\Endpoint\Order\DTO;

use DateTimeInterface;
use XCart\Extender\Mapping\Extender;
use XLite\API\Endpoint\Order\DTO\BaseOutput as ExtendedOutput;
use QSL\CustomerSatisfaction\API\Endpoint\Order\DTO\Survey\OrderCustomerSatisfactionSurveyOutput as SurveyOutput;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

/**
 * @Extender\Mixin
 */
class Output extends ExtendedOutput
{
    /**
     * @Assert\NotBlank
     * @Context(normalizationContext={DateTimeNormalizer::FORMAT_KEY: DateTime::ISO8601})
     * @var DateTimeInterface
     */
    public DateTimeInterface $survey_future_send_date;

    /**
     * @var SurveyOutput|null
     */
    public ?SurveyOutput $survey;
}
