<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Http\Requests\RoleRequest;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Returns all roles for Admin to see
        return Role::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(RoleRequest $request)
    {
        // Validate the data
        $validatedData = $request->validated();
        // Create a new role in the Role table
        $newRole = Role::create($validatedData->all());
        // Return success response
        return response()->json($newRole,201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        // Find role by its id
        $role = Role::findOrFail($id);
        // if found return result
        return response()->json($role,201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, string $id)
    {
        // Validate the data
        $validatedData = $request->validated();
        // Attempt to find role by its id to update
        $roleToUpdate = Role::findOrFail($id);
        // Update the role with the new validated data
        $roleToUpdate->update($validatedData->all());
        // Return the response
        return response()->json([
                'message' => 'Successfully updated role!',
                'role' => $roleToUpdate
            ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Find the role to delete
        $roleToDelete = Role::findOrFail($id);
        // Delete role
        $roleToDelete->delete();
        // return response
        return response()->json($roleToDelete,204);
    }
}
