<?php

namespace App\Http\Controllers\API;
// use OpenApi\Annotations as OA;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
/**
 * @OA\Tag(
 *     name="Register",
 *     description="Operations related to Register"
 * )
 */
class RegisterController extends BaseController
{
    // Register api
    /**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Register a new user",
 *     @OA\Parameter(
 *         name="name",
 *         in="query",
 *         description="User's name",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="email",
 *         in="query",
 *         description="User's email",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="password",
 *         in="query",
 *         description="User's password",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Parameter(
 *         name="c_password",
 *         in="query",
 *         description="User's c_password",
 *         required=true,
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(response="201", description="User registered successfully"),
 *     @OA\Response(response="422", description="Validation errors")
 * )
 */

    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->plainTextToken;
        $success['name'] =  $user->name;

        return $this->sendResponse($success, 'User register successfully.');
    }

    /**
* @OA\Post(
*     path="/api/login",
*     summary="Authenticate user and generate JWT token",
*     @OA\Parameter(
*         name="email",
*         in="query",
*         description="User's email",
*         required=true,
*         @OA\Schema(type="string")
*     ),
*     @OA\Parameter(
*         name="password",
*         in="query",
*         description="User's password",
*         required=true,
*         @OA\Schema(type="string")
*     ),
*     @OA\Response(response="200", description="Login successful"),
*     @OA\Response(response="401", description="Invalid credentials")
* )
*/
    // Login api
    public function login(Request $request): JsonResponse
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            $success['token'] =  $user->createToken('MyApp')->plainTextToken;
            $success['name'] =  $user->name;

            return $this->sendResponse($success, 'User login successfully.');
        }
        else{
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised']);
        }
    }
}
