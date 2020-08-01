<?php

namespace App\Repositories\Eloquent;

use App\Models\Design;
use App\Repositories\Contracts\DesignInterface;
use App\Repositories\Eloquent\BaseRepository;

class DesignRepository extends BaseRepository implements DesignInterface {

	public function model() {
		return Design::class;
	}
}