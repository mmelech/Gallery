<?php
/**
 * User Voter.
 */

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User Voter class.
 */
class UserVoter extends Voter
{
    /**
     * Edit user password.
     *
     * @const string
     */
    public const EDIT = 'EDIT';

    /**
     * Block user.
     *
     * @const string
     */
    public const BLOCK = 'BLOCK';

    /**
     * Edit user roles.
     *
     * @const string
     */
    public const EDIT_ROLE = 'EDIT_ROLE';

    /**
     * Security helper.
     */
    private Security $security;

    /**
     * OrderVoter constructor.
     *
     * @param Security $security Security helper
     */
    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    /**
     * Determines if the attribute and subject are supported by this voter.
     *
     * @param string $attribute An attribute
     * @param mixed  $subject   The subject to secure, e.g. an object the user wants to access or any other PHP type
     *
     * @return bool Result
     */
    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::EDIT, self::BLOCK, self::EDIT_ROLE])
            && $subject instanceof User;
    }

    /**
     * Perform a single access check operation on a given attribute, subject and token.
     * It is safe to assume that $attribute and $subject already passed the "supports()" method check.
     *
     * @param string         $attribute Permission name
     * @param mixed          $subject   Object
     * @param TokenInterface $token     Security token
     *
     * @return bool Vote result
     */
    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::EDIT:
                return $this->canEdit($subject, $user);
            case self::EDIT_ROLE:
                return $this->canEditRole($subject, $user);
            case self::BLOCK:
                return $this->canBlock($subject, $user);
        }

        return false;
    }

    /**
     * Checks if user can edit credentials.
     *
     * @param User $user        User entity
     * @param User $currentUser Current user
     *
     * @return bool Result
     */
    private function canEdit(User $user, UserInterface $currentUser): bool
    {
        return $user === $currentUser || $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * Checks if user can edit roles.
     *
     * @param User $user        User entity
     * @param User $currentUser Current user
     *
     * @return bool Result
     */
    private function canEditRole(User $user, UserInterface $currentUser): bool
    {
        return $user === $currentUser || $this->security->isGranted('ROLE_ADMIN');
    }

    /**
     * Checks if user can block.
     *
     * @param User $user        User entity
     * @param User $currentUser Current user
     *
     * @return bool Result
     */
    private function canBlock(User $user, UserInterface $currentUser): bool
    {
        return $user !== $currentUser && $this->security->isGranted('ROLE_ADMIN');
    }
}
