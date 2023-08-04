<?php
namespace App\Form\DataTransformers;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserToIdentifierTransformer implements DataTransformerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {
    }

    /**
     * Transforms an object (issue) to a string (number).
     *
     * @param  Issue|null $issue
     */
    public function transform($user): string
    {

        return $user;
    }


    public function reverseTransform($user): ?User
    {
        // no issue number? It's optional, so that's ok
        if (!$user) {
            return null;
        }

        $user = $this->entityManager
            ->getRepository(User::class)
            // query for the issue with this id
            ->findOneBy(['username'=>$user])
        ;

        if (null === $user) {
            // causes a validation error
            // this message is not shown to the user
            // see the invalid_message option
            throw new TransformationFailedException(sprintf(
                'An issue with user "%s" does not exist!',
                $user
            ));
        }

        return $user;
    }
}