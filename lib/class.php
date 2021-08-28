<?
class Arnly
{

	public function getHead ($arParams = [])
	{
		//session_start();
		?>
		<meta charset="utf-8">
		<link href="/edison/lib/vendors/bootstrap-5.1.0-dist/css/bootstrap.min.css" rel="stylesheet">
		<link href="/edison/css/style.css?v=<?=time()?>" rel="stylesheet">
		<script src="/edison/lib/vendors/bootstrap-5.1.0-dist/js/bootstrap.min.js"></script>
		<script src="/edison/lib/vendors/jquery/jquery-3.6.0.min.js"></script>
		<script src="/edison/js/script.js?v=<?=time()?>"></script>
		<?
	}

	public function connectDb () //$arParams = []
	{
		if (!file_exists( __DIR__.'/db.php'))
			return Arnly::showError(['MESSAGE_NAME' => 'ARNLY_CLASS_DB_FILE_NOT_FOUND']);
		
		require( __DIR__.'/db.php');

		if (!$DBPARAMS || !$DBPARAMS['HOST'] || !$DBPARAMS['DBNAME'] || !$DBPARAMS['USER'] || !$DBPARAMS['PASSWORD'])
			return Arnly::showError(['MESSAGE_NAME' => 'ARNLY_CLASS_NOT_PARAMS']);

		try {
			$arResult = new PDO('mysql:host='.$DBPARAMS['HOST'].';dbname='.$DBPARAMS['DBNAME'], $DBPARAMS['USER'], $DBPARAMS['PASSWORD']);
		} catch (PDOException $e) {
			return Arnly::showError(['MESSAGE_NAME' => 'ARNLY_CLASS_DB_CONNECT_ERROR'.': '.$e->getMessage()]);
		}
		
		return $arResult;
	}

	/*
	PARAMS:
	SESSION_ID
	*/
	public function getUserId ($arParams = [])
	{
		if (!$arParams['SESSION_ID']) {
			$arParams['SESSION_ID'] = session_id();
		}

		$DB = Arnly::connectDb();
		$SQL = 'SELECT * FROM users WHERE USER_SESSION="'.$arParams['SESSION_ID'].'" LIMIT 1';

		foreach ($DB->query($SQL) as $row)
		{
			$arResult['USER_ID'] = $row['ID'];
		}

		$DB = null;

		if (!$arResult['USER_ID']) {
			$arResult['USER_ID'] = Arnly::addUser();
		}

		return $arResult['USER_ID'];
	}

	public function addUser ()
	{
		$DB = Arnly::connectDb();
		$SQL = 'INSERT INTO users(ID, USER_SESSION) VALUES (null, "'.session_id().'")';

		if (!$DB->query($SQL))
		{
			$DB = null;
			return Arnly::showError(['MESSAGE_NAME' => 'ARNLY_CLASS_DB_QUERY_ERROR']);
		}
		else 
		{
			$arResult['USER_ID'] = $DB->lastInsertId();

			$SQL = 'INSERT INTO authority(ID, USER_ID, CURRENT_AUTORITY) VALUES (null, "'.$arResult['USER_ID'].'", "50")';
			if (!$DB->query($SQL))
			{
				$DB = null;
				return Arnly::showError(['MESSAGE_NAME' => 'ARNLY_CLASS_DB_QUERY_ERROR']);
			}
		}

		$DB = null;

		return $arResult['USER_ID'];
	}

	/*
	PARAMS:
	USER_ID
	NUMBER
	*/
	public function addData ($arParams = [])
	{
		$DB = Arnly::connectDb();
		$SQL = 'INSERT INTO history(ID, USER_ID, NUMBER) VALUES (null, "'.Arnly::getUserId().'", "'.$arParams['NUMBER'].'")';

		if (!$DB->query($SQL))
		{
			$DB = null;
			return Arnly::showError(['MESSAGE_NAME' => 'ARNLY_CLASS_DB_QUERY_ERROR']);
		}
		else
		{
			$arResult['ID'] = $DB->lastInsertId();
		}

		$DB = null;

		return $arResult['ID'];
	}

	/*
	PARAMS:
	
	*/
	public function getData ($arParams = [])
	{
		$arResult['NUMBER'] = '';

		$DB = Arnly::connectDb();
		$SQL = 'SELECT * FROM history WHERE CLOSED IS NULL ORDER BY ID ASC LIMIT 1';

		foreach ($DB->query($SQL) as $row)
		{
			$arResult = [
				'ID' => $row['ID'],
				'NUMBER' => $row['NUMBER'],
			];
		}

		$DB = null;

		return $arResult;
	}

	/*
	PARAMS:
	ID
	*/
	public function closedData ($arParams = [])
	{
		if (!$arParams['ID']) {
			return '';
		}

		$arResult['NUMBER'] = '';

		$DB = Arnly::connectDb();
		$SQL = 'UPDATE history SET CLOSED="1" WHERE ID="'.$arParams['ID'].'"';

		if (!$DB->query($SQL))
		{
			$DB = null;
			return Arnly::showError(['MESSAGE_NAME' => 'ARNLY_CLASS_DB_QUERY_ERROR']);
		}

		$DB = null;
	}

	/*
	PARAMS:
	ID
	*/
	public function getAnswers ($arParams = [])
	{
		$arResult['ANSWERS'] = [];
		$arParams['USERS_ANSWERS'] = [];

		$DB = Arnly::connectDb();
		$SQL = 'SELECT * FROM answers WHERE HISTORY_ID="'.$arParams['ID'].'"';

		foreach ($DB->query($SQL) as $row)
		{
			$arParams['USERS_ANSWERS'][$row['USER_ID']][] = $row['NUMBER'];
		}

		$DB = null;
		if (count($arParams['USERS_ANSWERS']) > 1)
		{
			foreach ($arParams['USERS_ANSWERS'] as $key => $row)
			{
				$arParams['VALUES'] = [];
				foreach ($row as $value) {
					$arParams['VALUES'][] = $value;
				}
				$arResult['ANSWERS'][] = 'Экстрасенс '.$key.' - '.implode(', ', $arParams['VALUES']);
			}
		}

		return $arResult['ANSWERS'];
	}

	/*
	PARAMS:
	ID
	*/
	public function getAnswersUsers ($arParams = [])
	{
		$arResult['USERS'] = [];

		$DB = Arnly::connectDb();
		$SQL = 'SELECT DISTINCT USER_ID FROM answers WHERE HISTORY_ID="'.$arParams['ID'].'"';

		foreach ($DB->query($SQL) as $row)
		{
			$arParams['USERS'][] = $row['USER_ID'];
		}

		$DB = null;

		return $arParams['USERS'];
	}

	/*
	PARAMS:
	USER_ID
	NUMBER
	ID
	*/
	public function addAnswers ($arParams = [])
	{
		$DB = Arnly::connectDb();
		$SQL = 'INSERT INTO answers(ID, USER_ID, NUMBER, HISTORY_ID) VALUES (null, "'.$arParams['USER_ID'].'", "'.$arParams['NUMBER'].'", "'.$arParams['ID'].'")';

		if (!$DB->query($SQL))
		{
			$DB = null;
			return Arnly::showError(['MESSAGE_NAME' => 'ARNLY_CLASS_DB_QUERY_ERROR']);
		}

		$DB = null;
	}

	/*
	PARAMS:
	USER_ID
	*/
	public function getHistory ($arParams = [])
	{
		$arResult['HISTORY'] = [];

		$DB = Arnly::connectDb();
		$SQL = 'SELECT * FROM history WHERE USER_ID="'.$arParams['USER_ID'].'"';

		foreach ($DB->query($SQL) as $row)
		{
			$arResult['HISTORY'][] = $row['NUMBER'];
		}

		$DB = null;

		return $arResult['HISTORY'];
	}

	/*
	PARAMS:
	HISTORY_ID
	USER_ID
	*/
	public function getAuthority ($arParams = [])
	{
		$arResult['AUTHORITY'] = [];
		$arParams['USERS_AUTHORITY'] = [];
		$arParams['USERS'] = Arnly::getAnswersUsers(['ID' => $arParams['HISTORY_ID']]);

		$DB = Arnly::connectDb();
		$SQL = 'SELECT * FROM authority WHERE USER_ID IN ('.implode(',', $arParams['USERS']).')';

		foreach ($DB->query($SQL) as $row)
		{
			$arParams['USERS_AUTHORITY'][$row['USER_ID']] = $row['CURRENT_AUTORITY'];
		}

		$DB = null;

		foreach ($arParams['USERS_AUTHORITY'] as $key => $value)
		{
			$arResult['AUTHORITY'][] = 'Экстрасенс '.$key.' - '.$value;
		}

		return $arResult['AUTHORITY'];
	}

	/*
	PARAMS:
	USER_ID
	SUCCSESS
	*/
	public function updateAuthority ($arParams = [])
	{
		$DB = Arnly::connectDb();
		$SQL = 'UPDATE authority SET CURRENT_AUTORITY=CURRENT_AUTORITY+'.$arParams['SUCCESS'].' WHERE USER_ID="'.$arParams['USER_ID'].'"';

		if (!$DB->query($SQL))
		{
			$DB = null;
			return Arnly::showError(['MESSAGE_NAME' => 'ARNLY_CLASS_DB_QUERY_ERROR']);
		}

		$DB = null;
	}

	//GET TEMPLATE
	/*
	PARAMS:
	TEMPLATE_NAME: .default, oneside, twoside
	*/
	public function getTemplate ($arParams = [])
	{
		if (!$arParams['TEMPLATE_NAME'])
			$arParams['TEMPLATE_NAME'] = '.default';

		if (!file_exists('/'.str_replace('/lib', '/templates', __DIR__).'/'.$arParams['TEMPLATE_NAME'].'/template.php'))
			return Arnly::showError(['MESSAGE_NAME' => 'ARNLY_CLASS_NOT_FOUND_TEMPLATE']);

		require_once('/'.str_replace('/lib', '/templates', __DIR__).'/'.$arParams['TEMPLATE_NAME'].'/template.php');
	}

	/*
	PARAMS:
	MESSAGE_NAME
	*/
	public function showError ($arParams = [])
	{
		if (!$arParams)
			return 'Произошла ошибка.'; 

		if (!$arParams['MESSAGE'])
			$arParams['MESSAGE'] = 'Произошла ошибка.';

		$arParams['MESSAGES'] = [
			'ARNLY_CLASS_DB_FILE_NOT_FOUND' => 'Не найдены параметры подключения к БД.',
			'ARNLY_CLASS_DB_CONNECT_ERROR' => 'Ошибка подключения к БД.',
			'ARNLY_CLASS_DB_QUERY_ERROR' => 'Ошибка выполнения запроса.',
			'ARNLY_CLASS_NOT_PARAMS' => 'Не переданы необходимые параметры.',
			'ARNLY_CLASS_NOT_FOUND_TEMPLATE' => 'Не найден шаблон вывода.',
			'ARNLY_TEMPLATE_NOT_DATA' => 'Нет данных.',
			'ARNLY_AJAX_NOT_FOUND_TEMPLATE' => 'Не указан шаблон.',
			'ARNLY_AJAX_NOT_SEND_DATA' => 'Не отправлены необходимые данные.',
			'ARNLY_AJAX_NUMBER_NOT_INT' => 'Число не является целым числом.',
			'ARNLY_AJAX_NUMBER_NOT_DOUBLE_DIGIT' => 'Переданное число не двухзначное.',
			'ARNLY_AJAX_WAIT_NUMBER' => 'Ждём число...',
			'ARNLY_AJAX_WAIT_ANSWERS' => 'Ждём экстрасенсов...',
		];

		return $arParams['MESSAGES'][$arParams['MESSAGE_NAME']] ?: htmlspecialchars($arParams['MESSAGE']);
	}
}
?>