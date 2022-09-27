<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Helpers\ApiHelper;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     * @OA\Post(
     * path="/api/v1/signup",
     * summary="Sign up",
     * description="Signup",
     * operationId="Signup",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Sign up",
     *    @OA\JsonContent(
     *       required={"email","password","first_name","last_name", "phone_number"},
     *        @OA\Property(property="first_name", type="string", maxLength=40, example="daniel"),
     *        @OA\Property(property="last_name", type="string", maxLength=40, example="chisom"),
     *        @OA\Property(property="username", type="string", maxLength=40, example="chisom"),
     *        @OA\Property(property="email", type="string", format="email", example="test@gmail.com"),
     *        @OA\Property(property="phone_number", type="string", maxLength=40, example="08012345678"),
     *        @OA\Property(property="password", type="string", format="password", example="chisonum"),
     *    ),
     * ),
     * @OA\Response(
     *    response=500,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something went wrong while processing your request."),
     *       @OA\Property(property="code", type="integer", example="500"),
     *       @OA\Property(property="success", type="boolean", example="false"),
     *       @OA\Property(property="error_debug", type="string", example="The provided credentials are incorrect."),
     *
     *     ),
     *  ),
     * )
     * @param RegisterRequest $request
     * @return JsonResponse
     */
    public function register(RegisterRequest $request)
    {
        // use Validator to validate the request
        try {
            // create user
            $user = User::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'username' => $request->username,
                'phone_number' => $request->phone_number,
                'email' => $request->email,
                'password' => bcrypt($request->password),
            ]);
            // generate token
            $token = $user->createToken('auth_token')->plainTextToken;
            // return response
            $message = 'User created successfully';

            return response()->json(['data' => $user, 'token' => $token, 'message' => $message, 'status' => 'SUCCESS'], 201);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), 500);
        }
    }

    /**
     * @OA\Post(
     * path="/api/v1/signin",
     * summary="Sign in",
     * description="Login with email and password",
     * operationId="Signin",
     * tags={"Auth"},
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *       required={"email","password"},
     *       @OA\Property(property="email", type="string", format="email", example="test@gmail.com"),
     *       @OA\Property(property="password", type="string", format="password", example="chisonum"),
     *    ),
     * ),
     * @OA\Response(
     *    response=500,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Something went wrong while processing your request."),
     *       @OA\Property(property="code", type="integer", example="500"),
     *       @OA\Property(property="success", type="boolean", example="false"),
     *       @OA\Property(property="error_debug", type="string", example="The provided credentials are incorrect."),
     *
     *     ),
     *  ),
     * )
     * @param LoginRequest $request
     * @return JsonResponse
     */
    public function login(LoginRequest $request)
    {
        // use Validator to validate the request
        try {
            // check if user exists
            $user = User::where('email', $request->email)->first();

            // if user does not exist
            if (! $user || ! Hash::check($request->password, $user->password)) {
                throw new Exception('The provided credentials are incorrect.');
            }

            // check if user is Active
            if ($user->status !== User::STATUS_ACTIVE) {
                throw new Exception('Your account is not active.');
            }

            // generate token
            $token = $user->createToken('auth_token')->plainTextToken;
            // return response
            $message = 'User logged in successfully';

            return response()->json(['data' => $user, 'token' => $token, 'message' => $message, 'status' => 'SUCCESS'], Response::HTTP_OK);
            // return ApiHelper::validResponse($message, $user, $token, 200);
        } catch (Exception $e) {
            $message = 'Something went wrong while processing your request.';

            return ApiHelper::invalidResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }
}
