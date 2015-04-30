<?php

namespace Application\Security\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ChatChannelVoter implements VoterInterface
{
    const VIEW = 'VIEW';
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
            self::VIEW,
            self::EDIT,
            self::REMOVE,
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = 'Application\Entity\ChatChannelEntity';

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

        if ($attribute == 'VIEW') {
            $accessGranted = false;
            $userChatChannels = $user->getChatChannels();

            if ($userChatChannels) {
                foreach ($userChatChannels as $userChatChannel) {
                    if ($object == $userChatChannel) {
                        $accessGranted = true;
                    }
                }
            }

            if (! $object->isPrivate()) {
                $accessGranted = true;
            }

            if ($accessGranted ||
                $object->getUser() == $this->app['user']) {
                return self::ACCESS_GRANTED;
            } else {
                return self::ACCESS_DENIED;
            }
        } elseif ($object->getUser() == $this->app['user']) {
            return self::ACCESS_GRANTED;
        } else {
            return self::ACCESS_DENIED;
        }
    }
}
