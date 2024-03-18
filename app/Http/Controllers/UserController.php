<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\RegisterUserRequest;

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


    // Authentication Methods

    // Register new user
    public function(RegisterUserRequest $request){
        // Checks input from request is valid
        $validator = $request->validated();
        // check if registration fails
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()] 400);
        }

        return response()->json(['message'=> 'User registered successfully']);
    }

    // Login (authenticate the user and return back a token)
    public function authenticate(Request $request) {

        $credentials = $request->only('email', 'password');

        // Check the auth credentials
        if(Auth::attempt($credentials)){
            $user = Auth::user();
            $token = $user->createToken("MyAppToken")->accessToken;

            // Now we must return the token
            return response()->json(['token' => $token],200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }

    // Logout
    public function logout(Request $request) {

        // Revoke the request
        $request()->user()->token()->revoke();
        // Return success message
        return response()->json(['message' => 'Successfully logged out!',200]);

    }

    // Refresh Token
    public function refresh(Request $request) {
        $user = $request->user();
        // Create a new token
        $token = $user->createToken("MyAppToken")->accessToken;

        // return token
        return response->json(['token' => $token],200);
    }


}
