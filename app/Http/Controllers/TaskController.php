<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Task;
use App\Http\Requests\TaskRequest;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tasks = Task::all();
        return response()->json($tasks,201);
    }

    /**
     * Show the form for storing a new resource.
     */
    public function store(TaskRequest $request)
    {
        // Validate data
        $validated = $request->validated();
        // Create new Task
        $newTask = Task::create($validated->all());
        // Return response
        return response()->json($newTask,200);
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find the task first with find or fail
        $task = Task::findOrFail($id);
        // If we find the task then return it:
        return response()->json($task,200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    // Specific User Role based functions
    public function userTasks(Request $request) {
        // Returns all the tasks for the auth user
        $user = $request->user();
        Log::info('Users Tasks User ID ', array($user->id));
        $userTasks = Task::where("user_id", $user->id)->get();

        return response()->json(
            ['Tasks' => $userTasks],
            200

        );

    }
}
