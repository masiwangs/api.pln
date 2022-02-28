<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;

class ProjectController extends Controller
{
    protected $response;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->response = new Response;
    }

    //
    public function index(Request $request) {
        $project = Project::query();

        if($request['prk'] == 1) {
            $project = $project->with('prks');
        }

        return $project->get();
    }

    public function create(Request $request) {
        $project = Project::create($request->all());

        if($project) {
            return $this->response->created($project);
        }

        return $this->response->bad_request();
    }

    public function update($project_id, Request $request) {
        $project = Project::find($project_id);
        
        if(!$project) {
            return $this->response->not_found();
        }

        $update = $project->update($request->all());

        if($update) {
            return $this->response->created($update);
        }

        return $this->response->bad_request();
    }

    public function delete($project_id) {
        $project = Project::find($project_id);

        if(!$project) {
            return $this->response->not_found();
        }

        $delete = $project->delete();

        if($delete) {
            return $this->response->success();
        }

        return $this->response->bad_request();
    }
}
