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

    var $plugin;

    function __construct($popularArticlePlugin, $contextId)
    {
        parent::__construct($popularArticlePlugin->getTemplateResource('settingsForm.tpl'));

        $this->contextId = $contextId;
        $this->plugin = $popularArticlePlugin;

        $this->setData('pluginName', $popularArticlePlugin->getName());

        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    function initData()
    {
        $popularArticlesDAO = DAORegistry::getDAO('PopularArticlesDAO');

        $popularArticle = $popularArticlesDAO->getById($this->contextId);

        if ($popularArticle) {
            $this->setData('title', $popularArticle->getTitle(null));
            $this->setData('days', $popularArticle->getDays());
            $this->setData('count', $popularArticle->getCount());
        }
    }

    function readInputData()
    {
        $this->readUserVars(array('title', 'days', 'count'));
    }

    function fetch($request, $template = null, $display = false)
    {
        return parent::fetch($request);
    }

    function execute(...$functionArgs)
    {
        $popularArticlesDAO = DAORegistry::getDAO('PopularArticlesDAO');
        $popularArticle = $popularArticlesDAO->getById($this->contextId);

        if ($popularArticle) {
            $popularArticle->setDays($this->getData('days'));
            $popularArticle->setCount($this->getData('count'));
            $popularArticle->setTitle($this->getData('title'), null);
            $popularArticlesDAO->updateObject($popularArticle);
        } else {
            $popularArticle = $popularArticlesDAO->newDataObject();
            $popularArticle->setContextId($this->contextId);
            $popularArticle->setDays($this->getData('days'));
            $popularArticle->setCount($this->getData('count'));
            $popularArticle->setTitle($this->getData('title'), null);
            $popularArticlesDAO->insertObject($popularArticle);
        }
        parent::execute(...$functionArgs);

        # empty current cache
        $cacheManager = CacheManager::getManager();
        $cache = $cacheManager->getCache('mostread', $this->contextId, array($this->plugin, '_cacheMiss'));
        $cache->flush();
    }
}
