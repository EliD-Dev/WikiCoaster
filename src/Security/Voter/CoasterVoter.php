<?php

namespace App\Security\Voter;
use App\Entity\Coaster;
use App\Entity\User;

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

final class CoasterVoter extends Voter{
    public const EDIT = 'EDIT';
    public const VIEW = 'VIEW';

    private readonly AuthorizationCheckerInterface $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    protected function supports(string $attribute, $subject): bool
    {
        // On supporte l'attribut 'EDIT' et le sujet est un coaster
        if ($attribute === self::EDIT && $subject instanceof Coaster) {
            return true;
        }

        return false;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser(); // On récupère l'utilisateur connecté

        /*$result = false;
    
        if ($user instanceof User) {
            // Si l'utilisateur est un administrateur, il peut tout faire
            if ($this->authorizationChecker->isGranted('ROLE_ADMIN')) {
                $result = true;
            }
            elseif ($attribute === 'EDIT' && $subject instanceof Coaster) {
                // Si l'attribut est 'EDIT' et que l'utilisateur est l'auteur du coaster, il peut modifier
                $result = $subject->getAuthor() === $user;
            }
        }*/
    
        return match ($attribute) {
            self::EDIT => $subject->getAuthor() == $user || $this->authorizationChecker->isGranted('ROLE_ADMIN'), // Si l'utilisateur est l'auteur du coaster ou un administrateur, il peut modifier
            self::VIEW => true, // On autorise la vue par défaut
            default => false, // On refuse par défaut
        };
    }
}
