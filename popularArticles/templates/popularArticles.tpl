{**
 * plugins/generic/popularArticles/templates/popularArticles.tpl
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Popular Articles plugin template
 *
 *}
{include file="frontend/components/header.tpl" }

<div class="page page_popularArticles">
    <div class="current_page_title">
        <h3 class="text-center">
            {translate key="plugins.generic.popularArticles.navMenuItem"}
        </h3>
    </div>
    <ul class="popular_articles">
    <div class="popular_articles_number_days">
        {translate key="plugins.generic.popularArticles.settings.days"}: {$mostReadDays}
    </div>
        {foreach from=$resultMetrics item=article}
            <li class="popular_article">
                <div class="popular_article_title"><a
                            href="{url journal=$article.journalPath page="article" op="view" path=$article.articleId}">{$article.articleTitle}{if !empty($article.articleSubTitle)} {$article.articleSubTitle}{/if}</a>
                </div>
                <div class="popular_article_journal"><span class="fa fa-eye"></span> {$article.metric}</div>
            </li>
        {/foreach}
    </ul>
</div>

{include file="frontend/components/footer.tpl"}
