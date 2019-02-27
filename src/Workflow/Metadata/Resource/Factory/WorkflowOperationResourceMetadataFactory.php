<?php

/*
 * This file is part of the "KeepMeATable" project.
 *
 * (c) Grégoire Hébert <gregoire@les-tilleuls.coop>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Workflow\Metadata\Resource\Factory;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Metadata\Resource\ResourceMetadata;

final class WorkflowOperationResourceMetadataFactory implements ResourceMetadataFactoryInterface
{
    private $supportsWorkflow;
    private $decorated;

    public function __construct(ResourceMetadataFactoryInterface $decorated, array $supportsWorkflow = [])
    {
        $this->supportsWorkflow = $supportsWorkflow;
        $this->decorated = $decorated;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $resourceClass): ResourceMetadata
    {
        $resourceMetadata = $this->decorated->create($resourceClass);

        if (!\in_array($resourceClass, $this->supportsWorkflow, true)) {
            return $resourceMetadata;
        }

        $operations = $resourceMetadata->getItemOperations();

        $operations['state'] = [
            'method' => 'PATCH',
            '_path_suffix' => '/state',
            'access_control' => 'is_granted("ROLE_USER")',
            'swagger_context' => [
                'summary' => 'Update the state.',
                'parameters' =>[[
                    'name' => 'waitingLine',
                    'in' => 'body',
                    'schema' => [
                        'type' => 'object',
                        'properties' => [
                            'state' => ['type' => 'string']
                        ],
                    ],
                    'example' => [
                       'state' => 'waiting'
                    ]
                ]]
            ]
        ];

        return $resourceMetadata->withItemOperations($operations);
    }
}
