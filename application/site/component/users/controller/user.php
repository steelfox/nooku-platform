<?php
/**
 * Nooku Framework - http://www.nooku.org
 *
 * @copyright	Copyright (C) 2011 - 2013 Johan Janssens and Timble CVBA. (http://www.timble.net)
 * @license		GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link		git://git.assembla.com/nooku-framework.git for the canonical source repository
 */

use Nooku\Library;

/**
 * User Controller
 *
 * @author  Gergo Erdosi <http://nooku.assembla.com/profile/gergoerdosi>
 * @package Component\Users
 */
class UsersControllerUser extends Library\ControllerModel
{
    public function __construct(Library\ObjectConfig $config)
    {
        parent::__construct($config);

        $this->addCommandCallback('before.edit', 'sanitizeRequest');
        $this->addCommandCallback('before.add' ,  'sanitizeRequest');
	}
    
    protected function _initialize(Library\ObjectConfig $config)
    {
        $config->append(array(
            'behaviors' => array(
                'editable', 'resettable', 'activatable',
                'com:activities.controller.behavior.loggable' => array('title_column' => 'name'),
        )));

        parent::_initialize($config);
    }

    public function getRequest()
    {
        $request = parent::getRequest();

        // Set request so that actions are made against logged user if none was given.
        if (!$request->query->get('id','int') && ($id = $this->getUser()->getId())) {
            $request->query->id = $id;
        }

        return $request;
    }

    public function sanitizeRequest(Library\ControllerContextInterface $context)
    {
        // Unset some variables because of security reasons.
        foreach(array('enabled', 'role_id', 'created_on', 'created_by', 'activation') as $variable) {
            $context->request->data->remove($variable);
        }
    }

    protected function _actionAdd(Library\ControllerContextInterface $context)
    {
        $params = $this->getObject('application.extensions')->users->params;
        $context->request->data->role_id = $params->get('new_usertype', 18);

        $user = parent::_actionAdd($context);

        if ($user->getStatus() == Library\Database::STATUS_CREATED)
        {
            $url = $this->getObject('application.pages')->getHome()->getLink();
            $this->getObject('application')->getRouter()->build($url);

            $context->response->setRedirect($url);
        }

        return $user;
    }

    protected function _actionEdit(Library\ControllerContextInterface $context)
    {
        $entity = parent::_actionEdit($context);

        $user = $this->getObject('user');

        // Logged user changed. Updated in memory/session user object.
        if ($context->response->getStatusCode() == HttpResponse::RESET_CONTENT && $entity->id == $user->getId()) {
            $user->setData($entity->getSessionData($user->isAuthentic()));
        }

        return $entity;
    }
}