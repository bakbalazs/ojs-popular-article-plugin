<?php

/**
 * @file classes/PopularArticle.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.popularArticles
 * @class PopularArticle
 * Data object representing a popular articles.
 */

class PopularArticle extends DataObject
{
    function getContextId()
    {
        return $this->getData('contextId');
    }

    function setContextId($contextId)
    {
        return $this->setData('contextId', $contextId);
    }

    function setTitle($title, $locale)
    {
        return $this->setData('title', $title, $locale);
    }

    function getTitle($locale)
    {
        return $this->getData('title', $locale);
    }

    function getLocalizedTitle()
    {
        return $this->getLocalizedData('title');
    }

    function getDays()
    {
        return $this->getData('days');
    }

    function setDays($days)
    {
        return $this->setData('days', $days);
    }

    function getCount()
    {
        return $this->getData('count');
    }

    function setCount($count)
    {
        return $this->setData('count', $count);
    }
}