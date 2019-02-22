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

namespace App\Workflow\DependencyInjection\Compiler;

use App\Workflow\Metadata\Resource\Factory\WorkflowOperationResourceMetadataFactory;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class WorkflowPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->getDefinition('workflow.registry');
        $factory = $container->getDefinition(WorkflowOperationResourceMetadataFactory::class);

        $arguments = [];

        foreach ($registry->getMethodCalls() as $methodCall) {
            $supportsStrategy = $methodCall[1][1];
            $arguments[] = $supportsStrategy->getArguments()[0];
        }

        $factory->setArgument(1, $arguments);
    }
}
