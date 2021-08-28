<input type="hidden" name="ID" value="">
<div class="container">
	<div class="row">
		<div class="d-flex col arnly-main-top" style="height: 100px">
			Вы на светлой стороне...
		</div>
	</div>
	<div class="row">
		<div class="d-flex col">
			Загадай число
			<div class="input-group">
			  <input type="text" name="arnly-oneside-value" class="form-control" placeholder="Введите двухзначное число">
			  <button class="btn btn-outline-success arnly-oneside-send" type="button">Подтвердить</button>
			</div>
		</div>
		<div class="d-flex col flex-column">
			Загаданные числа:<br>
			<span class="arnly-oneside-history">
				<?
				if (!$arResult['DATA'])
				{
					echo Arnly::showError(['MESSAGE_NAME' => 'ARNLY_TEMPLATE_NOT_DATA']);
				}
				?>
			</span>
		</div>
	</div>
	<div class="row">
		<div class="d-flex col">
			<span class="arnly-side-timer" style="display: none;">Обновление через: <span></span> секунд</span>
		</div>
		<div class="d-flex col flex-column">
			Ответы экстрасенсов:<br>
			<span class="arnly-oneside-answers">
			<?
			if (!$arResult['DATA'])
			{
				echo Arnly::showError(['MESSAGE_NAME' => 'ARNLY_TEMPLATE_NOT_DATA']);
			}
			?>
		</div>
	</div>
	<div class="row">
		<div class="d-flex col">
			<div class="arnly-log-wrapper"></div>
		</div>
		<div class="d-flex col flex-column">
			Авторитет экстрасенсов:<br>
			<span class="arnly-oneside-authority">
			<?
			if (!$arResult['DATA'])
			{
				echo Arnly::showError(['MESSAGE_NAME' => 'ARNLY_TEMPLATE_NOT_DATA']);
			}
			?>
			</span>
		</div>
	</div>
</div>