<?php

require_once "./vendor/autoload.php";
require_once "../../cmsimple/functions.php";
require_once "../../cmsimple/classes/PageDataRouter.php";
require_once "../../cmsimple/classes/Pages.php";

require_once "../plib/classes/SystemChecker.php";
require_once "../plib/classes/View.php";
require_once "../plib/classes/FakeSystemChecker.php";

require_once "./classes/Model.php";
require_once "./classes/Views.php";
require_once "./classes/InfoController.php";
require_once "./classes/MainAdminController.php";

const CMSIMPLE_XH_VERSION = "CMSimple_XH 1.7.5";

function XH_saveContents(): bool
{
    return true;
}
