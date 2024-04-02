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
        return response()->json($tasks,200);
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
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Validate the task data coming in
        $validatedData = $request->validated();
        // Try and find the task to update
        $task = Task::findOrFail($id);
        // Update the task
        $task->update($validatedData);
        // Return response
        return response()->json([
            'message' => 'Successfully updated task',
            'task' => $task,
        ], 201);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the task by its id and delete if exists
        $taskToDelete = Task::findOrFail($id);
        // Delete the task
        $taskToDelete->delete();
        // return the response 
        return response()->json(null, 204);
    }

    public function userTasks(Request $request) {
        // Returns all the tasks for the auth user
        $user = $request->user();
        // Return all the tasks associated with the user.
        $userTasks = $user->tasks();
        // Return sucess response
        return response()->json(['Tasks' => $userTasks],200);

    }
}
