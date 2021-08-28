<?
require_once(__DIR__.'/class.php');

header('Content-Type: application/json');

$arJsonData['error'] = [];
$arResult = [];
$arParams = [];
$arParams['USER_ID'] = Arnly::getUserId();
$arResult['ID'] = '';

if (!$_REQUEST['TEMPLATE'])
	$arJsonData['error'][] = Arnly::showError(['MESSAGE_NAME' => 'ARNLY_AJAX_NOT_FOUND_TEMPLATE']);

if (!$_REQUEST['NUMBER'] && !in_array($_REQUEST['TYPE'], ['UPDATE']))
	$arJsonData['error'][] = Arnly::showError(['MESSAGE_NAME' => 'ARNLY_AJAX_NOT_SEND_DATA']);

if (!in_array($_REQUEST['TYPE'], ['UPDATE']))
{
	if (!is_int((int)$_REQUEST['NUMBER']))
		$arJsonData['error'][] = Arnly::showError(['MESSAGE_NAME' => 'ARNLY_AJAX_NUMBER_NOT_INT']);
	else if (strlen($_REQUEST['NUMBER']) !== 2)
		$arJsonData['error'][] = Arnly::showError(['MESSAGE_NAME' => 'ARNLY_AJAX_NUMBER_NOT_DOUBLE_DIGIT']);
}


if (!$arJsonData['error'])
{
	if ($_REQUEST['TEMPLATE'] && in_array($_REQUEST['TEMPLATE'], ['ONESIDE']))
	{
		if (in_array($_REQUEST['TYPE'], ['ADD']))
		{
			$arResult['ID'] = Arnly::addData(['NUMBER' => $_REQUEST['NUMBER']]);
		}
		else if (in_array($_REQUEST['TYPE'], ['UPDATE']))
		{
			$arResult['ID'] = $_REQUEST['ID'];
		}

		$arResult['ANSWERS'] = Arnly::getAnswers(['ID' => $_REQUEST['ID']]);

		if (!$arResult['ANSWERS'])
		{
			$arResult['WAIT'] = Arnly::showError(['MESSAGE_NAME' => 'ARNLY_AJAX_WAIT_ANSWERS']);
		}

		if (!$arResult['WAIT'])
		{
			if ($_REQUEST['ID'])
				Arnly::closedData(['ID' => $_REQUEST['ID']]);
		}

		$arResult['HISTORY'] = Arnly::getHistory(['USER_ID' => $arParams['USER_ID']]);

		if (!$arResult['WAIT'])
		{
			if ($_REQUEST['ID'])
				$arResult['AUTHORITY'] = Arnly::getAuthority(['HISTORY_ID' => $_REQUEST['ID']]);
		}

		$arJsonData['success'] = 1;
	}

	if ($_REQUEST['TEMPLATE'] && in_array($_REQUEST['TEMPLATE'], ['TWOSIDE']))
	{
		$arParams['DATA'] = Arnly::getData();
		$arResult['ID'] = $arParams['DATA']['ID'];
		$arParams['NUMBER'] = $arParams['DATA']['NUMBER'];

		if (!$arParams['NUMBER'])
			$arResult['WAIT'] = Arnly::showError(['MESSAGE_NAME' => 'ARNLY_AJAX_WAIT_NUMBER']);

		if (!$arResult['WAIT']) {
			if (in_array($_REQUEST['TYPE'], ['ADD']))
			{
				if ($_REQUEST['NUMBER'] == $arParams['NUMBER'])
				{
					$arResult['ACCURACY'] = ['Правильно...'];
					Arnly::updateAuthority(['USER_ID' => $arParams['USER_ID'], 'SUCCESS' => 1]);
				}
				else
				{
					$arResult['ACCURACY'] = ['Неправильно!'];
					Arnly::updateAuthority(['USER_ID' => $arParams['USER_ID'], 'SUCCESS' => -1]);
				}

				Arnly::addAnswers(['USER_ID' => $arParams['USER_ID'], 'NUMBER' => $_REQUEST['NUMBER'], 'ID' => $arResult['ID']]);
			}
		}

		$arJsonData['success'] = 1;
	}
}

if ($arJsonData['error'])
	$arJsonData = ['result' => 'error', 'output' => '', 'message' => implode(', ', $arJsonData['error'])];
else if ($arJsonData['success'])
	$arJsonData = ['result' => 'success', 'output' => $arResult];

echo json_encode($arJsonData);
?>