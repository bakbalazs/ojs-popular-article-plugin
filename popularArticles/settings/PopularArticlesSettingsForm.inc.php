<?php

/**
 * @file plugins/generic/popularArticles/settings/PopularArticlesSettingsForm.inc.php
 *
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PopularArticlesSettingsForm
 * @ingroup plugins_generic_populararticles
 * @brief Wrapper for the Popular Articles plugin.
 *
 */

import('lib.pkp.classes.form.Form');

class PopularArticlesSettingsForm extends Form
{
    var $contextId;

//    var $popularArticleId;

    var $plugin;

    function __construct($popularArticlePlugin, $contextId)
    {
        parent::__construct($popularArticlePlugin->getTemplateResource('settingsForm.tpl'));


        $this->contextId = $contextId;
        $this->plugin = $popularArticlePlugin;
//        $this->popularArticleId = $popularArticleId;

        $this->setData('pluginName', $popularArticlePlugin->getName());

        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    function initData()
    {
        $plugin = $this->plugin;
        $context = Request::getContext();
        $contextId = $context ? $context->getId() : CONTEXT_ID_NONE;
        $popularArticlesDAO = DAORegistry::getDAO('PopularArticlesDAO');

//        $this->setData('title', $plugin->getSetting($contextId, 'title'));

        $popularArticle = $popularArticlesDAO->getById($contextId);

        if ($popularArticle) {
            $this->setData('title', $popularArticle->getTitle(null)); // Localized
            $this->setData('days', $popularArticle->getDays()); // Localized
            $this->setData('count', $popularArticle->getCount()); // Localized


        }


//        $this->setData('mostReadDays', $plugin->getSetting($contextId, 'mostReadDays'));
//        $this->setData('mostReadCount', $plugin->getSetting($contextId, 'mostReadCount'));

    }

    function readInputData()
    {
        $this->readUserVars(array('title', 'days', 'count'));
    }

    function fetch($request, $template = null, $display = false)
    {
        return parent::fetch($request);
    }

    function execute()
    {
        $popularArticlesDAO = DAORegistry::getDAO('PopularArticlesDAO');

        $plugin = $this->plugin;
        $context = Request::getContext();
        $contextId = $context ? $context->getId() : CONTEXT_ID_NONE;

        if ($contextId) {
            $popularArticle = $popularArticlesDAO->getById($contextId);
        } else {
            $popularArticle = $popularArticlesDAO->newDataObject();
            $popularArticle->setContextId($contextId);
        }


        $popularArticle->setTitle($this->getData('title'), null);
        $popularArticle->setDays($this->getData('days'));
        $popularArticle->setCount($this->getData('count'));

        if ($contextId) {
            $popularArticlesDAO->updateObject($popularArticle);
        } else {
            $popularArticlesDAO->insertObject($popularArticle);
        }

        # empty current cache
        $cacheManager = CacheManager::getManager();
        $cache = $cacheManager->getCache('mostread', $contextId, array($plugin, '_cacheMiss'));
        $cache->flush();
    }
}
