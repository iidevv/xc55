<?php
/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace Qualiteam\SkinActKlarna\Core\Endpoints;

class Assembler implements AssemblerInterface
{
    /**
     * @var string
     */
    private string $path;

    /**
     * @var array
     */
    private array $body;

    /**
     * @param \Qualiteam\SkinActKlarna\Core\Endpoints\Endpoint $endpoint
     */
    public function __construct(
        private Endpoint $endpoint
    )
    {
    }

    /**
     * Assembling a data to call endpoint
     *
     * @return void
     */
    public function assemble(): void
    {
        $this->assembleEndpoint();
    }

    /**
     * Assemble a endpoint
     *
     * @return void
     */
    protected function assembleEndpoint(): void
    {
        $this->assembleEndpointPath();
        $this->assembleEndpointBody();
    }

    /**
     * Assemble a path endpoint
     *
     * @return void
     */
    protected function assembleEndpointPath(): void
    {
        $this->endpoint->setPath(
            $this->path
        );
    }

    /**
     * Assemble a endpoint body
     *
     * @return void
     */
    protected function assembleEndpointBody(): void
    {
        $this->endpoint->setBody(
            $this->body
        );
    }

    /**
     * Set path
     *
     * @param string $value
     *
     * @return void
     */
    public function setPath(string $value): void
    {
        $this->path = $value;
    }

    /**
     * Set body
     *
     * @param array $value
     *
     * @return void
     */
    public function setBody(array $value): void
    {
        $this->body = $value;
    }
}