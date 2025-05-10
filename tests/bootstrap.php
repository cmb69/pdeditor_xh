<?php

require_once './vendor/autoload.php';
require_once '../../cmsimple/functions.php';
if (file_exists('../../cmsimple/classes/PageDataRouter.php')) {
    include_once '../../cmsimple/classes/PageDataRouter.php';
} else {
    include_once '../pluginloader/page_data/page_data_router.php';
}

require_once "./classes/Model.php";
require_once "./classes/Views.php";
require_once "./classes/Controller.php";

function XH_saveContents(): bool
{
    return true;
}
