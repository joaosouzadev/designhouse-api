<?php

namespace App\Http\Controllers\Designs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Design;
use App\Http\Resources\DesignResource;
use Storage;
use App\Repositories\Contracts\DesignInterface;
use App\Repositories\Eloquent\Criteria\LatestFirst;
use App\Repositories\Eloquent\Criteria\IsLive;
use App\Repositories\Eloquent\Criteria\ForUser;

class DesignController extends Controller
{
	protected $designInterface;

	public function __construct(DesignInterface $designInterface) {
		$this->designInterface = $designInterface;
	}

	public function index() {
		$designs = $this->designInterface->withCriteria([
            new LatestFirst(), 
            new IsLive(), 
            new ForUser(auth()->user()->id)
        ])->all();
		return DesignResource::collection($designs);
	}

	public function findDesign($id) {
		$design = $this->designInterface->find($id);
		return new DesignResource($design);
	}

	public function update(Request $request, $id) {

    	$design = $this->designInterface->find($id);

    	$this->authorize('update', $design);

    	$this->validate($request, [
    		'title' => ['required', 'unique:designs,title,' . $id],
    		'description' => ['required', 'string', 'min:10', 'max:150'],
    		'tags' => ['required']
    	]);

    	$design = $this->designInterface->update($id, [
    		'title' => $request->title,
    		'description' => $request->description,
    		'slug' => Str::slug($request->title),
    		'is_live' => !$design->upload_successful ? false : $request->is_live
    	]);

    	$this->designInterface->applyTags($id, $request->tags);

    	return new DesignResource($design);
	}

	public function destroy(Request $request, $id) {

    	$design = $this->designInterface->find($id);

    	$this->authorize('delete', $design);

    	foreach (['thumbnail', 'large', 'original'] as $size) {
    		if (Storage::disk($design->disk)->exists("uploads/designs/{$size}/" . $design->image)) {
    			Storage::disk($design->disk)->delete("uploads/designs/{$size}/" . $design->image);
    		}
    	}

    	$this->designInterface->delete($id);

    	return response()->json(['message' => 'Registro deletado', 200]);
	}
}
