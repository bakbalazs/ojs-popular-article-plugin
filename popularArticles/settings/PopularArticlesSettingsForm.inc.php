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

    function __construct($plugin)
    {
        $this->plugin = $plugin;
        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
        $this->setData('pluginName', $plugin->getName());
        
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    function initData()
    {
        $plugin = $this->plugin;
        $context = Request::getContext();
        $contextId = $context ? $context->getId() : CONTEXT_ID_NONE;
        $this->setData('mostReadDays', $plugin->getSetting($contextId, 'mostReadDays'));
        $this->setData('mostReadCount', $plugin->getSetting($contextId, 'mostReadCount'));

    }

    function readInputData()
    {
        $this->readUserVars(array('mostReadDays','mostReadCount'));
    }

    function fetch($request, $template = null, $display = false)
    {
        return parent::fetch($request);
    }

    function execute()
    {
        $plugin = $this->plugin;
        $context = Request::getContext();
        $contextId = $context ? $context->getId() : CONTEXT_ID_NONE;

        $plugin->updateSetting($contextId, 'mostReadDays', $this->getData('mostReadDays'), 'string');
        $plugin->updateSetting($contextId, 'mostReadCount', $this->getData('mostReadCount'), 'string');

        # empty current cache
        $cacheManager = CacheManager::getManager();
        $cache = $cacheManager->getCache('mostread', $contextId, array($plugin, '_cacheMiss'));
        $cache->flush();
    }
}
