<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
	public function register(Request $request)
	{
		$validator = Validator::make($request->all(), [
			'name' => 'required|max:50',
			'email' => 'required|email',
			'password' => 'required|min:6',
			'c_password' => 'required|same:password',
		]);

		if ($validator->fails()) {
			return response()->json(['error' => $validator->errors()], 401);
		}

		$reg = $request->only(['name', 'email', 'password']);
		$reg['password'] = bcrypt($reg['password']);

		$user = User::create($reg);

		return response()->json([
			'user' => $user,
			'token' => $user->createToken('clienex')->accessToken,
		]);
	}

	public function login(Request $request)
	{
		$status = 401;
		$response = ['error' => 'Unauthorised'];

		if (Auth::attempt($request->only(['email', 'password']))) {
			$status = 200;
			$response = [
				'user' => Auth::user(),
				'token' => Auth::user()->createToken('clienex')->accessToken,
			];
		}

		return response()->json($response, $status);
	}
}
