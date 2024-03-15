<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserStoreRequest;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserStoreRequest $request)
    {
        $validated = $request->validated();
   
        $newUser = User::create($validated->all());
        return response()->json($newUser,201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserStoreRequest $request, $id)
    {
        $validated = $request->validated();
        $user = User::findOrFail($id); // Attempts to get the user
        $user->update($validated->all()); // Update user with new data
        return response()->json($user,200); // Return reponse back
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find user to delete
        $userToRemove = User::findOrFail($id); // locate the user
        $userToRemove->delete(); // Delete the user
        return response()->json(null, 204); // return response back
    }
}
