<?php

/**
 * Copyright (c) 2012, Oxwall CandyStore
 * All rights reserved.

 * This software is intended for use with Oxwall Free Community Software http://www.oxwall.org/ and is
 * licensed under The BSD license.
 */

/**
 * Guests event handler
 *
 * @author Oxwall CandyStore <plugins@oxcandystore.com>
 * @package ow.ow_plugins.ocs_guests.bol
 * @since 1.3.1
 */

class OCSGUESTS_CLASS_EventHandler
{
    /**
     * Class instance
     *
     * @var OCSGUESTS_CLASS_EventHandler
     */
    private static $classInstance;
    
    /**
     * Class constructor
     *
     */
    private function __construct() { }

    /**
     * Returns class instance
     *
     * @return OCSGUESTS_CLASS_EventHandler
     */
    public static function getInstance()
    {
        if ( null === self::$classInstance )
        {
            self::$classInstance = new self();
        }

        return self::$classInstance;
    }


    public function trackVisit()
    {
        $attrs = OW::getRequestHandler()->getHandlerAttributes();

        if ( $attrs['controller'] == 'BASE_CTRL_ComponentPanel' && $attrs['action'] == 'profile' )
        {
            $username = $attrs['params']['username'];

            $user = BOL_UserService::getInstance()->findByUsername($username);
            $userId = $user->id;
            $viewerId = OW::getUser()->getId();

            if ( $viewerId && $viewerId != $userId )
            {
                OCSGUESTS_BOL_Service::getInstance()->trackVisit($userId, $viewerId);
            }
        }
    }

    public function onUserUnregister( OW_Event $event )
    {
        $params = $event->getParams();

        $userId = $params['userId'];

        OCSGUESTS_BOL_Service::getInstance()->deleteUserGuests($userId);
    }

    public function init()
    {
        $em = OW::getEventManager();

        $em->bind(OW_EventManager::ON_FINALIZE, array($this, 'trackVisit'));
        $em->bind(OW_EventManager::ON_USER_UNREGISTER, array($this, 'onUserUnregister'));
    }
}