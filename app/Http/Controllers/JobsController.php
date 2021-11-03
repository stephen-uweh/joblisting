<?php

namespace App\Http\Controllers;

use App\Http\Resources\JobsResource;
use App\Http\Resources\JobsResourceCollection;
use App\Models\Applications;
use App\Models\Jobs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class JobsController extends Controller
{
    public function all_jobs(): JobsResourceCollection{
        return new JobsResourceCollection(Jobs::paginate());
    }

    public function view_job(Jobs $job): JobsResource{
        return new JobsResource($job);
    }

    public function business_jobs(): JobsResourceCollection{
        $user = Auth::user();
        $jobs = Jobs::orderBy('created_at', 'desc')->where('business_id', $user->id)->get();
        return new JobsResourceCollection($jobs);
    }

    public function create_job(Request $request){
        $validation = Validator::make($request->all(),[
            'title' => 'required',
            'type' => 'required',
            'conditions' => 'required',
            'category' => 'required',
        ]);
        $data = $request->all();
        if($validation->fails()){
            $errors = $validation->errors();
            return response()->json([
                'success' => false,
                'message' => $errors
            ], 500);
        } else{
            $job = Jobs::create([
                'business_id' => Auth::user()->id,
                'title' => $data['title'],
                'type' => $data['type'],
                'conditions' => $data['conditions'],
                'category' => $data['category'],
            ]);
            return new JobsResource($job);
        }
    }

    public function update_job(Request $request, $job): JobsResource{
        $job = Jobs::findOrFail($job);
        $job->update($request->all());
        return new JobsResource($job);
    }

    public function delete_job($job){
        $job = Jobs::findOrFail($job);
        $job->delete();
        return response()->json([
            'success' => true,
            'message' => 'Job deleted'
        ], 200);
    }

    public function search(Request $request): JobsResourceCollection{
        $term = $request->term;
        $jobs = Jobs::orderBy('created_at', 'desc')->where('title', 'LIKE', "%$term%")->get();
        return new JobsResourceCollection($jobs);
    }

    public function apply(Request $request, $job){
        $job = Jobs::findOrFail($job);
        $validation = Validator::make($request->all(),[
            'fullname' => 'required',
            'email' => 'required',
            'phone' => 'required'
        ]);
        $data = $request->all();
        if($validation->fails()){
            $errors = $validation->errors();
            return response()->json([
                'success' => false,
                'message' => $errors
            ], 500);
        } else{
            Applications::create([
                'job_id' => $job->id,
                'fullname' => $data['fullname'],
                'email' => $data['email'],
                'phone' => $data['phone'],
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Application successful'
            ], 200);
        }
    }
}
