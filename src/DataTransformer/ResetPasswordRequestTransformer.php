<?php

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
        $holder = $this->holderRepository->findOneBy(['email'=>$object->email]);

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

        return ResetPasswordRequest::class === $to && ($context['input']['class'] ?? null) === ResetPassword::class;
    }
}
