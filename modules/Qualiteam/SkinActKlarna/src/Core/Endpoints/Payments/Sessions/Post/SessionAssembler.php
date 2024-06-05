<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints\Payments\Sessions\Post;

use Qualiteam\SkinActKlarna\Core\Endpoints\ConstructorInterface;
use Qualiteam\SkinActKlarna\Core\Endpoints\AssemblerInterface;

class SessionAssembler implements AssemblerInterface
{
    protected string $path = '';

    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\ConstructorInterface $constructorSession
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\AssemblerInterface   $assembler
     */
    public function __construct(
        private ConstructorInterface $constructorSession,
        private AssemblerInterface   $assembler
    ) {
        $this->constructorSession->build();
    }

    /**
     * Assembling a data to call endpoint
     */
    public function assemble(): void
    {
        $this->assembler->setBody(
            $this->getBody()
        );

        $this->assembler->setPath(
            $this->getPath()
        );

        $this->assembler->assemble();
    }

    /**
     * @return array
     */
    protected function getBody(): array
    {
        return $this->constructorSession->getBody();
    }

    /**
     * @return string
     */
    protected function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $value
     *
     * @return void
     */
    public function setPath(string $value): void
    {
        $this->path = $value;
    }
}