<?php

namespace Application\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ChatChannelMessageVoter implements VoterInterface
{
    const EDIT = 'EDIT';
    const REMOVE = 'REMOVE';

    private $app;

    public function __construct(\Silex\Application $app)
    {
        $this->app = $app;
    }

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::EDIT,
            self::REMOVE,
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = 'Application\Entity\ChatChannelMessageEntity';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    public function vote(TokenInterface $token, $object, array $attributes)
    {
        if (!$this->supportsClass(get_class($object))) {
            return self::ACCESS_ABSTAIN;
        }

        $user = $token->getUser();
        $attribute = $attributes[0];

        if (! ($user instanceof UserInterface)) {
            return self::ACCESS_ABSTAIN;
        }

        if ($this->app['security']->isGranted('ROLE_ADMIN') ||
            $object->getUser() == $this->app['user']) {
            return self::ACCESS_GRANTED;
        } else {
            return self::ACCESS_DENIED;
        }
    }
}
