<?php

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

        if (!in_array($resourceClass, $this->supportsWorkflow, true)) {
            return $resourceMetadata;
        }

        $operations = $resourceMetadata->getItemOperations();

        $operations['state'] = [
            'method' => 'PATCH',
            '_path_suffix' => '/state',
            'access_control' => 'is_granted("ROLE_USER")',
        ];

        return $resourceMetadata->withItemOperations($operations);
    }
}
