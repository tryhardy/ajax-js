<?php
namespace Local;

use Bitrix\Main\Context;

class Helper
{
	public static function ajaxBuffer($start = true, $buffer = true, $params = [], $ajaxKey = "")
	{
		global $APPLICATION;

		$request = Context::getCurrent()->getRequest();

		if (
			!(empty($ajaxKey) && $request->isAjaxRequest()) &&
			!(!empty($ajaxKey) && $request->get($ajaxKey) == "y")
		) {
			return false;
		}

		if ($start) {
			$APPLICATION->RestartBuffer();
			if ($buffer) ob_start();
		} else {
			// require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php";
			if (!$buffer) exit();

			$params["removeParams"] = (array)$params["removeParams"];
			if ($ajaxKey) $params["removeParams"][] = $ajaxKey;

			if (!array_key_exists("diffParams", $params)) {
				$params["diffParams"] = true;
			}

			$html = trim(ob_get_clean());

			if (Loader::includeModule("uplab.editor")) {
				if (class_exists(Surrogates::class)) {
					Surrogates::replaceOnBuffer($html);
				}
			}

			$result = [
				"html" => $html,

				"title" => $APPLICATION->GetPageProperty("title"),
				"h1"    => htmlspecialchars_decode($APPLICATION->GetTitle(false)),

				"url" => Uri::initWithRequestUri()
					->deleteSystemParams()
					->deleteParams($params["removeParams"])
					->updateParams($_GET)
					->getUri($params["diffParams"]),
			];

			if (!empty($params["data"])) {
				$result = array_merge($result, $params["data"]);
			}

			header('Content-Type: application/json');

			echo Json::encode(($result));
			\CMain::FinalActions();
			exit();
		}

		return true;
	}
}
