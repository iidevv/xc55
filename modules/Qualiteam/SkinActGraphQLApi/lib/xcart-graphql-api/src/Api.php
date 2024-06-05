<?php
// vim: set ts=4 sw=4 sts=4 et:

/**
 * Copyright (c) 2011-present Qualiteam software Ltd. All rights reserved.
 * See https://www.x-cart.com/license-agreement.html for license details.
 */

namespace XcartGraphqlApi;

use GraphQL\Error\FormattedError;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
use Includes\Logger\LoggerFactory;

class Api
{
    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @var InputInterface
     */
    private $input;
    /**
     * @var OutputInterface
     */
    private $output;
    /**
     * @var ContextInterface
     */
    private $context;

    public function __construct(InputInterface $input, ContextInterface $context, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->context = $context;
    }

    public function start()
    {
        $data = $this->input->getData();

        $data += ['query' => null, 'variables' => null, 'operationName' => null];

        $output = [];

        try {
            $schema = $this->getSchema();

            $httpCode = 200;
            $output = GraphQL::executeQuery(
                $schema,
                $data['query'],
                null,
                $this->context,
                (array) $data['variables'],
                $data['operationName']
            )->toArray();
        } catch (\Throwable $error) {
// @TODO: [FIXME]. Change logCustom according : https://www.notion.so/xc-eng/Logging-958173acf98a4644ab4d6a09e544c347#1917beaee2a64dcebf165e053b2cffdf
          //  \XLite\Logger::logCustom('graphql-errors', $error, 1);

            LoggerFactory::getLogger(['name' => 'xlite'])->log(300, $error);

            $httpCode = 500;
            $output['errors'] = [
                FormattedError::createFromException($error)
            ];
        }

        if ($this->shouldLogRequest($data)) {
            $this->log([
                'status' => ($httpCode === 200 ? 'success' : 'failure'),
                'query' => $data['query'],
                'variables' => $data['variables'],
                'operationName' => $data['operationName'],
                'output' => $output
            ]);
        }

        $this->output->output($httpCode, $output);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function shouldLogRequest($data)
    {
        return $data['operationName'] !== 'IntrospectionQuery'
            && strlen($data['query']) < 2000
            && strpos($data['query'], 'IntrospectionQuery') === false
            && strpos($data['query'], '__schema') === false;
    }

    /**
     * @param mixed $message
     */
    public function log($message)
    {
        LoggerFactory::getLogger(['name' => 'xlite'])->log(300, json_encode($message));
        //$this->getLogger()->debug($e->getMessage());

// @TODO: [FIXME]. Change logCustom according : https://www.notion.so/xc-eng/Logging-958173acf98a4644ab4d6a09e544c347#1917beaee2a64dcebf165e053b2cffdf
     //   \XLite\Logger::logCustom('graphql', $message);
    }

    /**
     * @return Schema
     * @throws \Exception
     */
    public function getSchema()
    {
        if (!$this->schema) {
            $this->schema = $this->createSchema();
        }

        return $this->schema;
    }

    /**
     * @return Schema
     * @throws \Exception
     */
    protected function createSchema()
    {
        return new Schema([
            'query' => Types::byName('query'),
            'mutation' => Types::byName('mutation'),
        ]);
    }
}
