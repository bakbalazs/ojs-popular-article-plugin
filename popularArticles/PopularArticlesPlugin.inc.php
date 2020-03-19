<?php

/**
 * @file plugins/generic/popularArticles/PopularArticlesPlugin.inc.php
 *
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PopularArticlesPlugin
 * @ingroup plugins_generic_populararticles
 * @brief Wrapper for the Popular Articles plugin.
 *
 */

import('lib.pkp.classes.plugins.GenericPlugin');
define('POPULAR_ARTICLES_NMI_TYPE', 'NMI_TYPE_POPULAR_ARTICLES');

class PopularArticlesPlugin extends GenericPlugin
{
    public function register($category, $path, $mainContextId = NULL)
    {
        $success = parent::register($category, $path);
        if (!Config::getVar('general', 'installed') || defined('RUNNING_UPGRADE')) return $success;
        if ($success && $this->getEnabled()) {
            import('plugins.generic.popularArticles.classes.PopularArticlesDAO');
            $popularArticlesDAO = new PopularArticlesDAO();
            DAORegistry::registerDAO('PopularArticlesDAO', $popularArticlesDAO);
            HookRegistry::register('LoadHandler', array($this, 'loadPageHandler'));
            HookRegistry::register('NavigationMenus::itemTypes', array($this, 'addMenuItemTypes'));
            HookRegistry::register('NavigationMenus::displaySettings', array($this, 'setMenuItemDisplayDetails'));
            HookRegistry::register('SitemapHandler::createJournalSitemap', array($this, 'addSitemapURLs'));
            $this->_registerTemplateResource();
        }
        return $success;
    }


    public function getDisplayName()
    {
        return __('plugins.generic.popularArticles.name');
    }

    public function getDescription()
    {
        return __('plugins.generic.popularArticles.description');
    }

    public function loadPageHandler($hookName, $args)
    {
        $page = $args[0];
        if ($page === 'popularArticles') {
            $this->import('handler/PopularArticlesHandler');
            define('HANDLER_CLASS', 'PopularArticlesHandler');
            return true;
        }
        return false;
    }

    function getActions($request, $actionArgs)
    {
        $router = $request->getRouter();
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        return array_merge(
            $this->getEnabled() ? array(
                new LinkAction(
                    'settings',
                    new AjaxModal(
                        $router->url($request, null, null, 'manage', null, array_merge($actionArgs, array('verb' => 'settings'))),
                        $this->getDisplayName()
                    ),
                    __('manager.plugins.settings'),
                    null
                ),
            ) : array(),
            parent::getActions($request, $actionArgs)
        );
    }

    function manage($args, $request)
    {
        $this->import('settings/PopularArticlesSettingsForm');

        switch ($request->getUserVar('verb')) {
            case 'settings':
                $context = $request->getContext();
                $settingsForm = new PopularArticlesSettingsForm($this, $context->getId());
                $settingsForm->initData();
                return new JSONMessage(true, $settingsForm->fetch($request));
            case 'save':
                $context = $request->getContext();
                $settingsForm = new PopularArticlesSettingsForm($this, $context->getId());
                $settingsForm->readInputData();
                if ($settingsForm->validate()) {
                    // Save the results
                    $settingsForm->execute();
                    return DAO::getDataChangedEvent();
                } else {
                    // Present any errors
                    return new JSONMessage(true, $settingsForm->fetch($request));
                }
//                if ($settingsForm->validate()) {
//                    $settingsForm->execute();
//                    $notificationManager = new NotificationManager();
//                    $notificationManager->createTrivialNotification(
//                        $request->getUser()->getId(),
//                        NOTIFICATION_TYPE_SUCCESS,
//                        array('contents' => __('plugins.popularArticles.settings.saved'))
//                    );
//                    return new JSONMessage(true);
//                }
                return new JSONMessage(true, $settingsForm->fetch($request));
        }
        return parent::manage($args, $request);
    }

    public function addMenuItemTypes($hookName, $args)
    {
        $types =& $args[0];
        $request = Application::getRequest();
        $context = $request->getContext();

        $types
        [MOST_READ_ARTICLE_NMI_TYPE] = array(
            'title' => __('plugins.generic.popularArticles.navMenuItem'),
            'description' => __('plugins.generic.popularArticles.navMenuItem.description'),

        );
    }

    public function setMenuItemDisplayDetails($hookName, $args)
    {
        $navigationMenuItem =& $args[0];
        $typePrefixLength = strlen(MOST_READ_ARTICLE_NMI_TYPE);
        if (substr($navigationMenuItem->getType(), 0, $typePrefixLength) === MOST_READ_ARTICLE_NMI_TYPE) {
            $request = Application::getRequest();
            $dispatcher = $request->getDispatcher();
            $navigationMenuItem->setUrl($dispatcher->url(
                $request,
                ROUTE_PAGE,
                null,
                'popularArticles',
                'view'
            ));
        }
    }

    function addSitemapURLs($hookName, $args)
    {
        $doc = $args[0];
        $rootNode = $doc->documentElement;
        $request = Application::getRequest();
        $context = $request->getContext();
        if ($context) {
            $url = $doc->createElement('url');
            $url->appendChild($doc->createElement('loc', htmlspecialchars($request->url($context->getPath(), 'popularArticles', 'view'), ENT_COMPAT, 'UTF-8')));
            $rootNode->appendChild($url);
        }
        return false;
    }

    function getInstallSchemaFile()
    {
        return $this->getPluginPath() . '/schema.xml';
    }

}
