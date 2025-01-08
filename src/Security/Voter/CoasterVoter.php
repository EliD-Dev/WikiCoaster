<?php

namespace App\Security\Voter;
use App\Entity\Coaster;
use App\Entity\User;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CoasterVoter extends Voter{
    public const EDIT = 'POST_EDIT';
    public const VIEW = 'POST_VIEW';

    private readonly AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // On supporte l'attribut 'EDIT' et le sujet est un coaster
        if ($attribute === 'EDIT' && $subject instanceof Coaster) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
    
        if (!$user instanceof User) {
            return false;
        }
    
        // Si l'utilisateur est un administrateur, il peut tout faire
        if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return true;
        }
    
        // Si l'attribut est 'EDIT' et que l'utilisateur est l'auteur du coaster, il peut modifier
        if ($attribute === 'EDIT' && $subject instanceof Coaster) {
            return $subject->getAuthor() === $user;
        }
    
        return false;
    }
}
