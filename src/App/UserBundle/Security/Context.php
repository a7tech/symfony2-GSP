<?php

/*
 * 
 */

namespace App\UserBundle\Security;

use Symfony\Component\Security\Core\SecurityContext;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

/**
 * A Security Context override to cleanup the roles array 
 */
class Context extends SecurityContext
{
    /**
     * Gets the currently authenticated token.
     *
     * @return TokenInterface|null A TokenInterface instance or null if no authentication information is available
     */
    public function getToken()
    {
        // No token, return
        if(!isset($this->token)){
            return;
        }
        if(is_null($this->token)){
            return $this->token;
        }

        if ($this->token instanceof UsernamePasswordToken) {
            // We check the roles set on the token and cleanup any roles that are just strings. 
            // This needs to be debugged more, but works for now
            $oldtoken = $this->token;
            $roles = $oldtoken->getRoles();
            foreach($roles as $key => $role){
                if(is_string($role)){
                    unset($roles[$key]);
                }
            }
            $newtoken = new UsernamePasswordToken(
                $oldtoken->getUser(), $oldtoken->getCredentials(), $oldtoken->getProviderKey(), $roles
            );
            $this->setToken($newtoken);
        }
        return $this->token;
    }

    /**
     * Sets the currently authenticated token.
     *
     * @param TokenInterface $token A TokenInterface token, or null if no further authentication information should be stored
     */
    public function setToken(TokenInterface $token = null)
    {
        $this->token = $token;
    }
}