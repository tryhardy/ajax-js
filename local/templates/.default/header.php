<?php

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// ...

$asset = \Bitrix\Main\Page\Asset::getInstance();
$assetsProgPath = "/dist/prog";
$asset->addJs("{$assetsProgPath}/app.js");

// ...
