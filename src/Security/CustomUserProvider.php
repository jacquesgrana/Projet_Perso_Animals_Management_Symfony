<?php
namespace App\Security;

use Symfony\Component\Security\Core\User\EntityUserProvider;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use App\Entity\User;
class CustomUserProvider extends EntityUserProvider
{
    // ...

    public function checkCredentials($credentials, UserInterface $user)
    {
        // Vérifiez si l'utilisateur est actif
        if (!$user->getActive()) {
            throw new CustomUserMessageAuthenticationException('Your account is not active.');
        }

        // Vérifiez le mot de passe comme d'habitude
        return parent::checkCredentials($credentials, $user);
    }

    public function loadUserByIdentifier($identifier)
    {
        $user = parent::loadUserByIdentifier($identifier);

        // Vérifiez si l'utilisateur est actif
        if (!$user->isActive()) {
            throw new CustomUserMessageAuthenticationException('Your account is not active.');
        }

        return $user;
    }
}

?>