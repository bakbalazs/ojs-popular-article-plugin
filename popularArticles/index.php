<?php

/**
 * @defgroup plugins_generic_populararticles
 */

/**
 * @file plugins/generic/popularArticles/index.php
 *
 * Copyright (c) 2014-2017 Simon Fraser University
 * Copyright (c) 2003-2017 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @ingroup plugins_generic_populararticles
 * @brief Wrapper for the Popular Articles plugin.
 *
 */
require_once('PopularArticlesPlugin.inc.php');
return new PopularArticlesPlugin();