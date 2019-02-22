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
use ApiPlatform\Core\Serializer\AbstractItemNormalizer;
use App\DTO\ResetPassword;
use App\Entity\ResetPasswordRequest;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ResetPasswordUpdateTransformer implements DataTransformerInterface
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function transform($object, string $to, array $context = [])
    {
        if (!$object instanceof ResetPassword) {
            throw new \LogicException(sprintf('Instance of ResetPassword expected, but got %s', \get_class($object)));
        }

        /** @var ResetPasswordRequest $resetPasswordRequest */
        $resetPasswordRequest = $context[AbstractItemNormalizer::OBJECT_TO_POPULATE];
        $user = $resetPasswordRequest->getUser();

        $resetPasswordRequest->setNewPassword(
            $this->passwordEncoder->encodePassword($user, $object->password)
        );

        return $resetPasswordRequest;
    }

    public function supportsTransformation($object, string $to, array $context = []): bool
    {
        if (null === ($context['item_operation_name'] ?? null)) {
            return false;
        }

        return ResetPasswordRequest::class === $to && ResetPassword::class === ($context['input']['class'] ?? null);
    }
}
