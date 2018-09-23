<?php

defined('_INDEX_EXEC') or die('Restricted access');

abstract class Controller {

	protected function getModel($model = null){
		if (!$model)
			$model[0] = chop(ucwords(App::get('component')), 's');
		else
			$model = explode('/', $model);

		$componentModelPath = APPROOT . '/components/';
		if (isset($model[1])) {
			$componentModelPath .= $model[0] . '/';
			$model[0] = $model[1];
			unset($model[1]);
		} else $componentModelPath .= App::get('component') . '/';

		if (file_exists($componentModelPath . $model[0] . '.php')) {
			require_once $componentModelPath . $model[0] . '.php';
			return new $model[0]();

		} else Output::fatal('Model does not exist');
	}
}