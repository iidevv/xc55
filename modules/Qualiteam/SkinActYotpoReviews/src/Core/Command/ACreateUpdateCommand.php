<?php

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActYotpoReviews\Core\Command;

use Qualiteam\SkinActYotpoReviews\Core\Configuration\Configuration;
use Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointInterface;
use Qualiteam\SkinActYotpoReviews\Core\Factory\LoggerFactory;
use Qualiteam\SkinActYotpoReviews\Core\TopMessage;
use XCart\Container;
use XLite\Core\Database;
use XLite\Core\Translation;
use XLite\Model\AEntity;

abstract class ACreateUpdateCommand
{
    protected array $result = [];

    /**
     * @param \Qualiteam\SkinActYotpoReviews\Core\Endpoints\EndpointInterface $container
     * @param \XLite\Model\AEntity|null                                       $entity
     */
    public function __construct(
        protected EndpointInterface $container,
        protected ?AEntity $entity = null,
    ) {
    }

    abstract protected function executeCommand(): void;

    public function execute(): void
    {
        if ($this->isModuleConfigured()) {
            $this->executeCommand();
        } else {
            $this->logNotConfiguredError();
        }
    }

    /**
     * @param \XLite\Model\AEntity|null $entity
     *
     * @return void
     */
    protected function getResultYotpoRequest(?AEntity $entity = null): void
    {
        $this->entity = $entity ?? $this->entity;

        $this->result = $this->container->getData(
            $this->entity
        );
    }

    protected function isModuleConfigured(): bool
    {
        $config = $this->getConfigContainer();

        return $config?->getAppKey()
            && $config?->getSecretKey();
    }

    protected function getConfigContainer(): ?Configuration
    {
        return Container::getContainer()?->get('yotpo.reviews.configuration');
    }

    /**
     * @return void
     */
    protected function showError(): void
    {
        TopMessage::addYotpoError(
            $this->getErrorMessage()
        );
    }

    /**
     * @return string
     */
    protected function getErrorMessage(): string
    {
        $arr = isset($this->result['message']) ? json_decode($this->result['message'], true) : null;

        return $arr ? $arr['errors'][0]['message'] ?? $arr['message'] : $this->getDefaultErrorMessage();
    }

    /**
     * @return string
     */
    protected function getDefaultErrorMessage(): string
    {
        return Translation::lbl('SkinActYotpoReviews something went wrong get more into a log file');
    }

    /**
     * @return void
     * @throws \Exception
     */
    protected function updateEntity(): void
    {
        Database::getEM()->flush();
    }

    /**
     * @return void
     */
    protected function clearEntity(): void
    {
        Database::getEM()->clear();
    }

    /**
     * @return void
     */
    protected function persistEntity(): void
    {
        Database::getEM()->persist($this->entity);
    }

    /**
     * @param string $key
     *
     * @return void
     */
    protected function setYotpoId(string $key): void
    {
        $this->entity->setYotpoId(
            $this->getYotpoId($key)
        );
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getYotpoId(string $key): string
    {
        return $this->result[$key]['yotpo_id'];
    }

    /**
     * @return void
     */
    protected function setIsYotpoSync(): void
    {
        $this->entity->setIsYotpoSync(true);
    }

    /**
     * @return bool
     */
    protected function isErrorResult(): bool
    {
        return !$this->isSuccess();
    }

    /**
     * @return bool
     */
    protected function isSuccess(): bool
    {
        return $this->result['success'] !== false;
    }

    /**
     * @return void
     */
    protected function logError(): void
    {
        LoggerFactory::logger()->error(
            $this->getErrorMessage()
        );
    }

    protected function logNotConfiguredError(): void
    {
        LoggerFactory::logger()->error(
            $this->getNotConfiguredErrorMessage()
        );
    }

    protected function getNotConfiguredErrorMessage(): string
    {
        return Translation::lbl('SkinActYotpoReviews a module yotporeviews is not configured');
    }
}
