<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Jobs\SendWelcomeEmail;
use Illuminate\Database\Eloquent\ModelNotFoundException;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::all();
        return response()->json($users,201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return response()->json($user, 200);

        } catch (ModelNotFoundException $exception) {
            
            return response()->json(['message' => 'User not found!'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $validatedData = $request->validated();
        $user = User::findOrFail($id); // Attempts to get the user
        $user->update($validatedData); // Update user with new data
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

    // Authentication Functions for User
    public function register(UserRequest $request){
        // Checks input from request is valid
        $validatedData = $request->validated();
        // Create the user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role_id' => 2, // User by default
            
        ]);

        // Attempt to generate a new token for the given user using unique id
        $token = Auth::guard('api')->tokenById($user->id);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Dispatch new email for new user
        dispatch(new SendWelcomeEmail($user));


        return response()->json([
            'message'=> 'User registered successfully',
            'token' => $token
        
        ], 201);
    }
    // Login (authenticate the user and return back a token)
    public function authenticate (Request $request) {

        $credentials = $request->only('email', 'password');

        // Check the auth credentials
        if(Auth::attempt($credentials)){
            $user = Auth::user();
            // Generates a new stateless JWT token
            $token = Auth::guard('api')->tokenById($user->id);

            // Now we must return the token
            return response()->json([
                'message' => "Successfully Logged in!",
                'token' => $token
            
            ],200);
        } else {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

    }
    // Logout
    public function logout(Request $request) {

        // Get the token from the request
        $token = JWTAuth::getToken();
        
        if($token) {
            // Invalidate JWT token
            JWTAuth::invalidate($token);
        }
        // Return success message
        return response()->json(['message' => 'Successfully logged out!',200]);

    }


    public function refresh(Request $request) {
        // Refresh the token
        $newToken = JWTAuth::refresh(JWTAuth::getToken());
        
        // return token
        return response->json([
            'message' => 'Token refreshed successfully',
            'token' => $token
            
            ]
            ,200);
    }

    // User Role Specific Functions
    public function userDetails(Request $request) {
        // Return only the details of the auth user in question
        $user = $request->user();
        return response()->json(['User details' => $user], 200);
    }


}
