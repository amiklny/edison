<?
require_once(__DIR__.'/lib/class.php');
?>
<!DOCTYPE html>
<html>
	<head>
		<?
		echo Arnly::getHead();
		?>
	</head>
	<body>
		<?
		if (in_array($_REQUEST['T'], ['ONESIDE', 'TWOSIDE']))
		{
			$arParams['TEMPLATE_NAME'] = strtolower($_REQUEST['T']);
		}

		echo Arnly::getTemplate([
			'TEMPLATE_NAME' => $arParams['TEMPLATE_NAME'] ?: '',
		]);
		?>
	</body>
</html>