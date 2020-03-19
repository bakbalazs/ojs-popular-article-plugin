<?php

/**
 * @file plugins/generic/popularArticles/handler/PopularArticlesHandler.inc.php
 *
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class PopularArticlesHandler
 * @ingroup plugins_generic_populararticles
 * @brief Wrapper for the Popular Articles plugin.
 *
 */
import('classes.handler.Handler');

class PopularArticlesHandler extends Handler
{

    function view($args, $request)
    {
        $templateMgr = TemplateManager::getManager($request);
        $this->setupTemplate($request);
        $context = $request->getContext();

        $cacheManager = CacheManager::getManager();
        $cache = $cacheManager->getCache('mostread', $context->getId(), array($this, '_cacheMiss'));
        $daysToStale = 1;

        if (time() - $cache->getCacheTime() > 60 * 60 * 24 * $daysToStale) {
            $cache->flush();
        }
        $resultMetrics = $cache->getContents();

        $popularArticlesDAO = DAORegistry::getDAO('PopularArticlesDAO');

        $popularArticle = $popularArticlesDAO->getById($context->getId());

        $title = $popularArticle->getLocalizedTitle();

        if (empty($title)) {
            $title = "";
        }

        $templateMgr->assign('title', $title);

        $templateMgr->assign('resultMetrics', $resultMetrics);

        $plugin = PluginRegistry::getPlugin('generic', 'populararticlesplugin');

        $mostReadDays = $popularArticle->getDays();
        if (empty($mostReadDays)) {
            $mostReadDays = 30;
        }

        $templateMgr->assign('mostReadDays', $mostReadDays);

        return $templateMgr->display($plugin->getTemplateResource('popularArticles.tpl'));
    }


    function _cacheMiss($cache)
    {
        $publishedArticleDao = DAORegistry::getDAO('PublishedArticleDAO');
        $journalDao = DAORegistry::getDAO('JournalDAO');
        $request = Application::getRequest();
        $context = $request->getContext();
        $plugin = PluginRegistry::getPlugin('generic', 'populararticlesplugin');
        $popularArticlesDAO = DAORegistry::getDAO('PopularArticlesDAO');

        $popularArticle = $popularArticlesDAO->getById($context->getId());

        $mostReadDays = $popularArticle->getDays();
        if (empty($mostReadDays)) {
            $mostReadDays = 30;
        }

        $dayString = "-" . $mostReadDays . " days";
        $daysAgo = date('Ymd', strtotime($dayString));
        $currentDate = date('Ymd');

        $filter = array(
            STATISTICS_DIMENSION_CONTEXT_ID => $context->getId(),
            STATISTICS_DIMENSION_ASSOC_TYPE => ASSOC_TYPE_SUBMISSION_FILE,
        );
        $filter[STATISTICS_DIMENSION_DAY]['from'] = $daysAgo;
        $filter[STATISTICS_DIMENSION_DAY]['to'] = $currentDate;
        $orderBy = array(STATISTICS_METRIC => STATISTICS_ORDER_DESC);
        $column = array(
            STATISTICS_DIMENSION_SUBMISSION_ID,
        );

        $mostReadCount = $popularArticle->getCount();
        if (empty($mostReadCount)) {
            $mostReadCount = 15;
        }
        import('lib.pkp.classes.db.DBResultRange');
        $dbResultRange = new DBResultRange($mostReadCount);
        $metricsDao =& DAORegistry::getDAO('MetricsDAO');
        $result = $metricsDao->getMetrics(OJS_METRIC_TYPE_COUNTER, $column, $filter, $orderBy, $dbResultRange);
        foreach ($result as $resultRecord) {
            $submissionId = $resultRecord[STATISTICS_DIMENSION_SUBMISSION_ID];
            $article = $publishedArticleDao->getById($submissionId);
            $journal = $journalDao->getById($article->getJournalId());
            $articles[$submissionId]['journalPath'] = $journal->getPath();
            $articles[$submissionId]['articleId'] = $article->getBestArticleId();
            $articles[$submissionId]['articleTitle'] = $article->getLocalizedTitle();
            $articles[$submissionId]['metric'] = $resultRecord[STATISTICS_METRIC];
        }
        $cache->setEntireCache($articles);
        return $result;
    }

}