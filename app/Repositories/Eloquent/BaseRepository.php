<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\BaseInterface;
use App\Exceptions\ModelNotDefined;

abstract class BaseRepository implements BaseInterface {


	protected $model;

	public function __construct() {
		$this->model = $this->getModelClass();
	}

	protected function getModelClass() {
		if (!method_exists($this, 'model')) {
			throw new ModelNotDefined('Model nao definido');
		}

		return app()->make($this->model());
	}

	public function all() {
		return $this->model::all();
	}
}