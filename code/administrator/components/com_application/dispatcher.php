<?php
/**
 * @version     $Id$
 * @package     Nooku_Server
 * @subpackage  Application
 * @copyright   Copyright (C) 2007 - 2012 Johan Janssens. All rights reserved.
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.nooku.org
 */

/**
 * Application Dispatcher Class
.*
 * @author      Johan Janssens <http://nooku.assembla.com/profile/johanjanssens>
 * @package     Nooku_Server
 * @subpackage  Application
 */
class ComApplicationDispatcher extends KDispatcherApplication
{
    /**
     * The site identifier.
     *
     * @var string
     */
    protected $_site;

    /**
     * The application message queue.
     *
     * @var	array
     */
    protected $_message_queue = array();

    /**
     * The application options
     *
     * @var KConfig
     */
    protected $_options = null;

    /**
     * Constructor.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     */
    public function __construct(KConfig $config)
    {
        parent::__construct($config);

        //Register the default exception handler
        $this->getEventDispatcher()->addEventListener(
            'onException', array($this, 'exception'), KEvent::PRIORITY_LOW
        );

        //Set callbacks
        $this->registerCallback('before.run', array($this, 'loadConfig'));
        $this->registerCallback('before.run', array($this, 'loadSession'));
        $this->registerCallback('before.run', array($this, 'loadLanguage'));

        // Set the connection options
        $this->_options = $config->options;

        //Setup the request
        KRequest::root(str_replace('/administrator', '', KRequest::base()));

        //Set the site name
        if(empty($config->site)) {
            $this->_site = $this->_findSite();
        } else {
            $this->_site = $config->site;
        }
    }

    /**
     * Initializes the options for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param 	object 	An optional KConfig object with configuration options.
     * @return 	void
     */
    protected function _initialize(KConfig $config)
    {
        $config->append(array(
            'event_dispatcher'  => 'com://admin/debug.event.dispatcher.debug',
            'event_subscribers' => array('com://admin/application.event.subscriber.unauthorized'),
            'site'     => null,
            'options'  => array(
                'session_name' => 'admin',
                'config_file'  => JPATH_ROOT.'/configuration.php',
                'language'     => null
            ),
        ));

        parent::_initialize($config);
    }

    /**
     * Run the application
     *
     * @param KCommandContext $context	A command context object
     */
    protected function _actionRun(KCommandContext $context)
    {
        //Set the site error reporting
        $this->getEventDispatcher()->setDebugMode($this->getCfg('debug_mode'));

        //Set the site debug mode
        define( 'KDEBUG', $this->getCfg('debug') );

        //Set the paths
        $params = $this->getService('application.components')->files->params;

        define('JPATH_FILES'  , JPATH_SITES.'/'.$this->getSite());
        define('JPATH_IMAGES' , JPATH_SITES.'/'.$this->getSite().'/'.$params->get('image_path', 'images'));
        define('JPATH_CACHE'  , $this->getCfg('cache_path', JPATH_ROOT.'/cache'));

        // Set timezone to user's setting, falling back to global configuration.
        $timezone = new DateTimeZone($context->user->get('timezone', $this->getCfg('timezone')));
		date_default_timezone_set($timezone->getName());

        //Route the request
        $this->route();
    }

    /**
     * Route the request
     *
     * @param KCommandContext $context	A command context object
     */
    protected function _actionRoute(KCommandContext $context)
    {
        $url = clone $context->request->getUrl();

        //Parse the route
        $this->getRouter()->parse($url);

        //Set the request
        $context->request->query->add($url->query);

        //Set the controller to dispatch
        $component = substr( $context->request->query->get('option', 'cmd', 'com_dashboard'), 4);

        if(!empty($component))
        {
            if (!$this->getService('application.components')->isEnabled($component)) {
                throw new KControllerExceptionNotFound('Component Not Enabled');
            }

            $this->setController('com://admin/'.$component.'.dispatcher');
        }
        else throw new KControllerExceptionNotFound('Component Not Found');

        //Authorize the request
        $this->dispatch();
    }

    /**
     * Dispatch the request
     *
     * @param KCommandContext $context	A command context object
     */
    protected function _actionDispatch(KCommandContext $context)
    {
        $this->getController()->dispatch($context);

        //Render the page
        if(!$context->response->isRedirect() && $context->request->getFormat() == 'html')
        {
            $config = array('response' => $context->response);

            $this->getService('com://admin/application.controller.page', $config)
                ->render(array('tmpl' => $context->request->query->get('tmpl', 'cmd', 'default')));
        }

        //Send the response
        $this->send();
    }

    /**
     * Render an exception
     *
     * @throws InvalidArgumentException If the action parameter is not an instance of KExceptionInterface
     * @param KCommandContext $context	A command context object
     */
    protected function _actionException(KCommandContext $context)
    {
        //Check an exception was passed
        if(!isset($context->param) && !$context->param instanceof KEventException)
        {
            throw new InvalidArgumentException(
                "Action parameter 'exception' [KEventException] is required"
            );
        }

        //Render the exception
        $config = array('response' => $context->response);

        $this->getService('com://admin/application.controller.exception', $config)
             ->render(array('exception' => $context->param->getException()));

        //Send the response
        $this->send();
    }

    /**
     * Load the configuration
     *
     * @param KCommandContext $context	A command context object
     * @return	void
     */
    public function loadConfig(KCommandContext $context)
    {
        // Check if the site exists
        if($this->getService('com://admin/sites.model.sites')->getRowset()->find($this->getSite()))
        {
            //Load the application config settings
            JFactory::getConfig()->loadArray($this->_options->toArray());

            //Load the global config settings
            require_once( $this->_options->config_file );
            JFactory::getConfig()->loadObject(new JConfig());

            //Load the site config settings
            require_once( JPATH_SITES.'/'.$this->getSite().'/settings.php');
            JFactory::getConfig()->loadObject(new JSettings());

        }
        else throw new KControllerExceptionNotFound('Site :'.$this->getSite().' not found');
    }

    /**
     * Load the user session or create a new one
     *
     * Old sessions are flushed based on the configuration value for the cookie lifetime. If an existing session,
     * then the last access time is updated. If a new session, a session id is generated and a record is created
     * in the #__users_sessions table.
     *
     * @param KCommandContext $context	A command context object
     * @return	void
     */
    public function loadSession(KCommandContext $context)
    {
        $session = $context->user->session;

        //Set Session Name
        $session->setName(md5($this->getCfg('secret').$this->getCfg('session_name')));

        //Set Session Lifetime
        $session->setLifetime($this->getCfg('lifetime', 15) * 60);

        //Set Session Handler
        $session->setHandler('database', array('table' => 'com://admin/users.database.table.sessions'));

        //Set Session Options
        $session->setOptions(array(
            'cookie_path'   => (string) KRequest::base(),
            'cookie_secure' => $this->getCfg('force_ssl') == 2 ? true : false
        ));

        //Auto-start the session if a cookie is found
        if(!$session->isActive())
        {
            if ($context->request->cookies->has($session->getName())) {
                $session->start();
            }
        }

        //Re-create the session if we changed sites
        if($context->user->isAuthentic() && ($session->site != $this->getSite()))
        {
            if(!$this->getService('com://admin/users.controller.session')->add()) {
                $session->destroy();
            }
        }
    }

    /**
     * Get the application languages.
     *
     * @return	\ComLanguagesDatabaseRowsetLanguages
     */
    public function loadLanguage(KCommandContext $context)
    {
        $languages = $this->getService('application.languages');
        $language = null;

        // If a language was specified it has priority.
        if($iso_code = $this->_options->language)
        {
            $result = $languages->find(array('iso_code' => $iso_code));
            if(count($result) == 1) {
                $language = $result->top();
            }
        }

        // Otherwise use user language setting.
        if(!$language && $iso_code = $context->user->get('language'))
        {
            $result = $languages->find(array('iso_code' => $iso_code));
            if(count($result) == 1) {
                $language = $result->top();
            }
        }

        // If language still not set, use the primary.
        if(!$language) {
            $language = $languages->getPrimary();
        }

        $languages->setActive($language);

        // TODO: Remove this.
        JFactory::getConfig()->setValue('config.language', $language->iso_code);
    }

    /**
     * Get the application router.
     *
     * @param  array $options 	An optional associative array of configuration options.
     * @return	\ComApplicationRouter
     */
    public function getRouter(array $options = array())
    {
        $router = $this->getService('com://admin/application.router', $options);
        return $router;
    }

    /**
     * Gets a configuration value.
     *
     * @param	string	$name    The name of the value to get.
     * @param	mixed	$default The default value
     * @return	mixed	The user state.
     */
    public function getCfg( $name, $default = null )
    {
        return JFactory::getConfig()->getValue('config.' . $name, $default);
    }

    /**
     * Gets the name of site
     *
     * @return	string
     */
    public function getSite()
    {
        return $this->_site;
    }

    /**
     * Get the template
     *
     * @return string The template name
     */
    public function getTemplate()
    {
        return 'default';
    }

    /**
     * Enqueue a system message.
     *
     * @param	string 	$msg 	The message to enqueue.
     * @param	string	$type	The message type.
     */
    function enqueueMessage( $msg, $type = 'message' )
    {
        // For empty queue, if messages exists in the session, enqueue them first
        if (!count($this->_message_queue))
        {
            $session_queue = $this->getUser()->get('application.queue');

            if (count($session_queue))
            {
                $this->_message_queue = $session_queue;
                $this->getUser()->remove('application.queue');
            }
        }

        // Enqueue the message
        $this->_message_queue[] = array('message' => $msg, 'type' => strtolower($type));
    }

    /**
     * Get the system message queue.
     *
     * @return	The system message queue.
     */
    function getMessageQueue()
    {
        // For empty queue, if messages exists in the session, enqueue them
        if (!count($this->_message_queue))
        {
            $session_queue = $this->getUser()->get('application.queue');

            if (count($session_queue))
            {
                $this->_message_queue = $session_queue;
                $this->getUser()->set('application.queue', null);
            }
        }

        return $this->_message_queue;
    }

    /**
     * Find the site name
     *
     * This function tries to get the site name based on the information present in the request.
     * If no site can be found it will return 'default'.
     *
     * @return string   The site name
     */
    protected function _findSite()
    {
        // Check URL host
        $uri  = clone(JURI::getInstance());
        $site = 'default';

        $host = $uri->getHost();
        if(!$this->getService('com://admin/sites.model.sites')->getRowset()->find($host))
        {
            if($this->getRequest()->isGet()) {
                $request = $this->getRequest()->getQuery()->get('site', 'cmd');
            } else {
                $request = $this->getRequest()->getData()->get('site', 'cmd');
            }

            if($request)
            {
                if($this->getService('com://admin/sites.model.sites')->getRowset()->find($request)) {
                    $site = $request;
                }
            }

        } else $site = $host;

        return $site;
    }
}
