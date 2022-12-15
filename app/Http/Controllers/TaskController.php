<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Traits\HttpResponses;
use App\Models\Task;
use App\Http\Resources\TaskResource;
use App\Http\Requests\StoreTaskRequest;

class TaskController extends Controller
{
    use HttpResponses;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TaskResource::collection(
            Task::where('user_id', auth()->user()->id)->get()
        );
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        $request->validated($request->all());

        $task = Task::create([
            'user_id' => auth()->user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority,
        ]);
        // return  new TaskResource($task);
        return response()->json(
            [
                'message' => 'Task created successfully',
                'task' => new TaskResource($task)
            ],
            201
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        // if (auth()->id() !== $task->user->id) {
        //     return $this->error('', 'You are not authorized to view this task', 401);
        // }
        // return new TaskResource($task);


        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        if (auth()->id() == $task->user->id) {
            $task->update($request->all());
            return response()->json(
                [
                    'message' => 'Task updated successfully',
                    'task' => new TaskResource($task)
                ],
                200
            );
        }
        return $this->error('', 'You are not authorized to update this task', 401);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        return $this->isNotAuthorized($task) ? $this->isNotAuthorized($task) : $task->delete();
    }

    private function isNotAuthorized($task)
    {
        if (auth()->id() !== $task->user->id) {
            return $this->error('', 'You are not authorized to view this task', 401);
        }
    }
}
