<?php

namespace Goat\AccountBundle\Security\Authorization\Voter;

use Goat\AccountBundle\Entity\Account;
use Goat\AccountBundle\Security\User\GoatUser;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class AccountVoter extends Voter
{
    /**
     * View the album or its medias
     */
    const VIEW = 'view';

    /**
     * Edit the album
     */
    const EDIT = 'edit';

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof Account && in_array($attribute, [self::VIEW, self::EDIT]);
    }

    /**
     * {@inheritdoc}
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        $isAnonymous = is_string($user);

        if (!$isAnonymous && !$user instanceof GoatUser) {
            return false;
        }
        if (!$subject instanceof \Goat\AccountBundle\Entity\Account) {
            return false;
        }

        $isAuthorized = false;

        switch ($attribute) {

            case self::VIEW:
                // @todo For view, check for friendship
            case self::EDIT:

                // Either the user is owner, or user
                if ($subject->getId() === $user->getAccount()->getId()) {
                    $isAuthorized = true;
                } else {
                    // FIXME: use roles instead
                    $isAuthorized = $user->getAccount()->isAdmin();
                }
                break;
        }

        return $isAuthorized;
    }
}
