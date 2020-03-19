<?php

/**
 * @file classes/PopularArticlesDAO.inc.php
 *
 * Copyright (c) 2014-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @package plugins.generic.popularArticles
 * @class PopularArticlesDAO
 * Operations for retrieving and modifying PopularArticles objects.
 */

import('lib.pkp.classes.db.DAO');
import('plugins.generic.popularArticles.classes.PopularArticle');

class PopularArticlesDAO extends DAO
{

    function getById($contextId)
    {
        $result = $this->retrieve(
            'SELECT * FROM popular_articles WHERE context_id = ?',
            (int)$contextId
        );

        $returner = null;
        if ($result->RecordCount() != 0) {
            $returner = $this->_fromRow($result->GetRowAssoc(false));
        }
        $result->Close();
        return $returner;
    }

    function insertObject($popularArticle)
    {
        $this->update(
            'INSERT INTO popular_articles (context_id) VALUES (?)',
            array(
                (int)$popularArticle->getContextId(),
            )
        );

        $popularArticle->setId($this->getInsertId());
        $this->updateLocaleFields($popularArticle);

        return $popularArticle->getId();
    }

    function updateObject($popularArticle)
    {
        $this->update(
            'UPDATE	popular_articles
			SET	context_id = ?
			WHERE popular_article_id = ?',
            array(
                (int)$popularArticle->getContextId(),
                (int)$popularArticle->getId()
            )
        );
        $this->updateLocaleFields($popularArticle);
    }


    function _fromRow($row)
    {
        $popularArticle = $this->newDataObject();
        $popularArticle->setId($row['popular_article_id']);
        $popularArticle->setContextId($row['context_id']);

        $this->getDataObjectSettings('popular_articles_settings', 'popular_article_id', $row['popular_article_id'], $popularArticle);

        return $popularArticle;
    }

    function getInsertId()
    {
        return $this->_getInsertId('popular_articles', 'popular_article_id');
    }

    function newDataObject()
    {
        return new PopularArticle();
    }

    function getLocaleFieldNames()
    {
        return array('title');
    }

    function getAdditionalFieldNames()
    {
        return array('days', 'count');
    }

    function updateLocaleFields($popularArticle)
    {
        $this->updateDataObjectSettings('popular_articles_settings', $popularArticle, array(
            'popular_article_id' => $popularArticle->getId()
        ));
    }
}