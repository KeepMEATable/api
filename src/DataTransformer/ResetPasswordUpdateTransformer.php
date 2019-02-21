<?php

declare(strict_types=1);

namespace App\DataTransformer;

use ApiPlatform\Core\DataTransformer\DataTransformerInterface;
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use App\DTO\ResetPassword;
use App\Entity\ResetPasswordRequest;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordUpdateTransformer implements DataTransformerInterface
{
    private $passwordEncoder;
    private $registry;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder, RegistryInterface $registry)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->registry = $registry;
    }

    public function transform($object, string $to, array $context = [])
    {
        /** @var ResetPasswordRequest $resetPasswordRequest */
        $resetPasswordRequest = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE];
        $user = $resetPasswordRequest->getUser();

        $resetPasswordRequest->setNewPassword(
            $this->passwordEncoder->encodePassword($user, $object->password)
        );

        $em = $this->registry->getEntityManagerForClass(ResetPasswordRequest::class);

        if (null === $em) {
            throw new \RuntimeException('Cannot delete this reset password request.');
        }

        $em->remove($resetPasswordRequest);
        $em->flush();

        return $resetPasswordRequest;
    }

    public function supportsTransformation($object, string $to, array $context = []): bool
    {
        if (null === ($context['item_operation_name'] ?? null)) {
            return false;
        }

        return ResetPasswordRequest::class === $to && ($context['input']['class'] ?? null) === ResetPassword::class;
    }
}
