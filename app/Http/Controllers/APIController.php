<?php

namespace App\Http\Controllers;

use App\Models\User;
use Dotenv\Validator as DotenvValidator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class APIController extends Controller
{
    public function getUsers($id = null)
    {
        if (empty($id)) {
            $users = User::get();
            return response()->json(['users' => $users], 200);
        } else {
            $users = User::find($id);
            return response()->json(['users' => $users], 200);
        }
    }

    public function getUsersList(Request $request)
    {
        $header = $request->header('Authorization');
        if (empty($header)) {
            $message = 'Header Authorization is missing';
            return response()->json(['status' => false, 'message' => $message], 422);
        } else {
            if ($header == "Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6Imxlb2RhbiIsImlhdCI6MTUxNjIzOTAyMn0.JYwPq91XVtnQxA79exAAUpnqyOURfIpvPVK8ipa7-eU") {
                $users = User::get();
                return response()->json(['users' => $users], 200);
            } else {
                $message = 'Header Authorization is icorrect';
                return response()->json(['status' => false, 'message' => $message], 422);
            }
        }

    }

    public function addUser(Request $request)
    {
        if ($request->isMethod('POST')) {
            $userData = $request->input();
            // Check user details
            /*if (empty($userData['name']) || empty($userData['email']) || empty($userData['password'])) {
                $error_message = 'Please enter complete user details!';
            }

            // Check if email is valid
            if (!filter_var($userData['email'], FILTER_VALIDATE_EMAIL)) {
                $error_message = 'Please enter valid Email!';
            }

            // Check if User Email Already Exists
            $userCount = User::where('email', $userData['email'])->count();
            if ($userCount > 0) {
                $error_message = 'Email already exists!';
            }

            if (isset($error_message) && !empty($error_message)) {
                return response()->json(['status' => false, 'message' => $error_message], 422);
            }*/

            $rules = [
                'name' => 'required|regex:/^[\pL\s\-]+$/u',
                'email' => 'required|email|unique:users',
                'password' => 'required'
            ];

            $customMessages = [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email' => 'Valid Email is required',
                'email.unique' => 'Email already exists in database',
                'password.required' => 'Password is required',
            ];

            $validator = Validator::make($userData, $rules, $customMessages);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $user = new User();
            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->password = bcrypt($userData['password']);
            $user->save();
            return response()->json(['message' => 'User added successfully'], 201);
        }
    }

    public function addUsers(Request $request)
    {
        if ($request->isMethod('POST')) {

            $userData = $request->input();

            $rules = [
                'users.*.name' => 'required|regex:/^[\pL\s\-]+$/u',
                'users.*.email' => 'required|email|unique:users',
                'users.*.password' => 'required'
            ];

            $customMessages = [
                'users.*.name.required' => 'Name is required',
                'users.*.email.required' => 'Email is required',
                'users.*.email.email' => 'Valid Email is required',
                'users.*.email.unique' => 'Email already exists in database',
                'users.*.password.required' => 'Password is required',
            ];

            $validator = Validator::make($userData, $rules, $customMessages);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            foreach ($userData['users'] as $key => $value) {
                $user = new User();
                $user->name = $value['name'];
                $user->email = $value['email'];
                $user->password = bcrypt($value['password']);
                $user->save();
            }
            return response()->json(['message' => 'Users added successfully'], 201);
        }
    }

    public function updateUser(Request $request, $id)
    {
        if ($request->isMethod('PUT')) {
            $userData = $request->input();
            //echo '<pre>'; print_r($userData); die;
            $rules = [
                'name' => 'required|regex:/^[\pL\s\-]+$/u',
                'password' => 'required'
            ];

            $customMessages = [
                'name.required' => 'Name is required',
                'password.required' => 'Password is required',
            ];

            $validator = Validator::make($userData, $rules, $customMessages);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            User::where('id', $id)->update(['name'=>$userData['name'], 'password'=>bcrypt($userData['password'])]);
            return response()->json(['message' => 'User details updated successfully!'], 202);
        }
    }

    public function updateUserName(Request $request, $id)
    {
        if ($request->isMethod('PATCH')) {
            $userData = $request->input();
            //echo '<pre>'; print_r($userData); die;
            $rules = [
                'name' => 'required|regex:/^[\pL\s\-]+$/u'
            ];

            $customMessages = [
                'name.required' => 'Name is required',
            ];

            $validator = Validator::make($userData, $rules, $customMessages);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            User::where('id', $id)->update(['name'=>$userData['name']]);
            return response()->json(['message' => 'User details updated successfully!'], 202);
        }
    }

    public function deleteUser($id)
    {
        User::where('id', $id)->delete();
        return response()->json(['message' => 'User deleted successfully'], 202);
    }

    public function deleteUserJson(Request $request)
    {
        if ($request->isMethod('DELETE')) {
            $userData = $request->all();
            User::where('id', $userData['id'])->delete();
            return response()->json(['message' => 'User deleted successfully'], 200);
        }
    }
    public function deleteUserMulti($ids)
    {
        $ids= explode(',', $ids);
        User::whereIn('id', $ids)->delete();
        return response()->json(['message' => 'User deleted successfully'], 202);
    }

    public function registerUser(Request $request)
    {
        if ($request->isMethod('POST')) {
            $userData = $request->input();

            $rules = [
                'name' => 'required|regex:/^[\pL\s\-]+$/u',
                'email' => 'required|email|unique:users',
                'password' => 'required'
            ];

            $customMessages = [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'email.email' => 'Valid Email is required',
                'email.unique' => 'Email already exists in database',
                'password.required' => 'Password is required',
            ];

            $validator = Validator::make($userData, $rules, $customMessages);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            $apiToken = Str::random(60);

            $user = new User();
            $user->name = $userData['name'];
            $user->email = $userData['email'];
            $user->password = bcrypt($userData['password']);
            $user->access_token = $apiToken;
            $user->save();
            return response()->json(['status' => true, 'message' => 'User added successfully', 'token' => $apiToken], 201);
        }
    }

    public function loginUser(Request $request)
    {
        if ($request->isMethod('POST')) {
            $userData = $request->input();

            $rules = [
                'email' => 'required|email|exists:users',
                'password' => 'required'
            ];

            $customMessages = [
                'email.required' => 'Email is required',
                'email.email' => 'Valid Email is required',
                'email.unique' => 'Email does not exists in database',
                'password.required' => 'Password is required',
            ];

            $validator = Validator::make($userData, $rules, $customMessages);

            if ($validator->fails()) {
                return response()->json($validator->errors(), 422);
            }

            // Fetch User Details
            $userDetails = User::where('email', $userData['email'])->first();

            // Verify the Password
            if (password_verify($userData['password'], $userDetails->password)) {
                // Update Token
                $apiToken = Str::random(60);

                // Update Token
                User::where('email', $userData['email'])->update(['access_token' => $apiToken]);
                return response()->json(['status' => true, 'message' => 'User logged in successfully', 'token' => $apiToken], 201);
            } else {
                return response()->json(['status' => false, 'message' => 'Password is incorrect'], 422);
            }
        }
    }

    public function logoutUser(Request $request)
    {
        $access_token = $request->header('Authorization');
        if (empty($access_token)) {
            $message = 'User Token is missing in API Header';
            return response()->json(['status' => false, 'message' => $message], 422);
        } else {
            $access_token = str_replace('Bearer ', '', $access_token);
            $userCount= User::where('access_token', $access_token)->count();
            if ($userCount > 0) {
                User::where('access_token', $access_token)->update(['access_token' => NULL]);
                $message= 'User Logged out Successfully!';
                return response()->json(['status' => true, 'message' => $message], 200);
            }
        }
    }
}
