{**
 * plugins/generic/popularArticles/templates/settingsForm.tpl
 *
 * Copyright (c) 2013-2019 Simon Fraser University
 * Copyright (c) 2003-2019 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * Popular Articles plugin settings
 *
 *}
<script type="text/javascript">
    $(function () {ldelim}
        $('#popularArticlesSettingsForm').pkpHandler('$.pkp.controllers.form.AjaxFormHandler');
        {rdelim});
</script>

<form class="pkp_form" id="popularArticlesSettingsForm" method="post"
      action="{url op="manage" category="generic" plugin=$pluginName verb="save"}">
    {csrf}

    {include file="controllers/notification/inPlaceNotification.tpl" notificationId="popularArticlesFormNotification"}

    {fbvFormArea id="popularArticlesDisplayOptions" title="plugins.generic.popularArticles.settings.title"}

    {fbvFormSection for="mostReadDays"}
        {fbvElement type="text" label="plugins.generic.popularArticles.settings.days"  placeholder="plugins.generic.popularArticles.settings.days.placeholder"  id="mostReadDays" value=$mostReadDays}
    {/fbvFormSection}

    {fbvFormSection for="mostReadCount"}
        {fbvElement type="text" label="plugins.generic.popularArticles.settings.count"  placeholder="plugins.generic.popularArticles.settings.count.placeholder"  id="mostReadCount" value=$mostReadCount}
    {/fbvFormSection}

    {/fbvFormArea}

    {fbvFormButtons id="WGLSettingsFormSubmit" submitText="common.save" hideCancel=true}

</form>
