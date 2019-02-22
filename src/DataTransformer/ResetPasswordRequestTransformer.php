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

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use App\DTO\ResetPassword;
use App\Entity\ResetPasswordRequest;
use App\Repository\HolderRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResetPasswordRequestTransformer implements DataTransformerInterface
{
    private $holderRepository;

    public function __construct(HolderRepository $holderRepository)
    {
        $this->holderRepository = $holderRepository;
    }

    public function transform($object, string $to, array $context = [])
    {
        $holder = $this->holderRepository->findOneBy(['email' => $object->email]);

        if (null === $holder) {
            throw new NotFoundHttpException('This email is not registered, is there a typo?');
        }

        $resetPasswordRequest = new ResetPasswordRequest();
        $resetPasswordRequest->setUser($holder);

        return $resetPasswordRequest;
    }

    public function supportsTransformation($object, string $to, array $context = []): bool
    {
        if (null === ($context['collection_operation_name'] ?? null)) {
            return false;
        }

        return ResetPasswordRequest::class === $to && ResetPassword::class === ($context['input']['class'] ?? null);
    }
}
