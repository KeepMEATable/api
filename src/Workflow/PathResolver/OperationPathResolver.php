<?php

declare(strict_types=1);

namespace App\Workflow\PathResolver;

use ApiPlatform\Core\PathResolver\OperationPathResolverInterface;

final class OperationPathResolver implements OperationPathResolverInterface
{
    private $decorated;

    public function __construct(OperationPathResolverInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function resolveOperationPath(string $resourceShortName, array $operation, $operationType/*, string $operationName = null*/): string
    {
        $path = $this->decorated->resolveOperationPath($resourceShortName, $operation, $operationType, null);

        if (!isset($operation['_path_suffix'])) {
            return $path;
        }

        return str_replace('{id}', '{id}'.$operation['_path_suffix'], $path);
    }
}
