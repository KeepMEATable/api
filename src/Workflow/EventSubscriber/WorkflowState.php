<?php

declare(strict_types=1);

namespace App\Workflow\EventSubscriber;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use ApiPlatform\Core\EventListener\EventPriorities;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Workflow\Registry;

final class WorkflowState implements EventSubscriberInterface
{
    private $workflows;

    public function __construct(Registry $workflows)
    {
        $this->workflows = $workflows;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [['applyState', EventPriorities::POST_DESERIALIZE]]
        ];
    }

    public function applyState(GetResponseEvent $event)
    {
        $request = $event->getRequest();

        if (!$request->isMethod(Request::METHOD_PATCH)
            || !($attributes = RequestAttributesExtractor::extractAttributes($request))
            || !isset($attributes['item_operation_name'])
            || 'state' !== $attributes['item_operation_name']
        ) {
            return;
        }

        $requestContent = json_decode($request->getContent());

        if (!isset($requestContent->state) || !($state = $requestContent->state)) {
            throw new BadRequestHttpException('State is required.');
        }

        $class = $request->attributes->get('data');
        $workflow = $this->workflows->get($class);

        if (!$workflow->can($class, $state)) {
            throw new ValidationException(new ConstraintViolationList([
                new ConstraintViolation(sprintf("Transition '%s' cannot be applied.", $state), '', [], $class->getMarking(), 'marking', $state)
            ]));
        }

        $workflow->apply($class, $state);
    }
}
