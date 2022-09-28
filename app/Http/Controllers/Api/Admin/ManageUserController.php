<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateUserStatusRequest;
use App\Models\User;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ManageUserController extends Controller
{
    /**
     * @OA\Put(
     * path="/api/v1/user/{id}",
     * summary="update user status",
     * description="update user status",
     * operationId="update user",
     * tags={"Admin/Manage-users"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="ID of user to update",
     *    in="path",
     *    name="id",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    description="update the status field",
     *    @OA\JsonContent(
     *       required={"status"},
     *       @OA\Property(property="status", type="string", format="text", example="active"),
     *    ),
     * ),
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not authorized"),
     *    )
     * )
     * )
     */
    public function updateUserStatus(UpdateUserStatusRequest $request, $id)
    {
        try {
            $user = User::find($id);
            $user->status = $request->status;
            $user->save();

            return response()->json([
                'data' => $user,
                'status' => 'success',
                'message' => 'User status updated successfully',
            ]);
        } catch (\Exception $e) {
            $message = 'Something went wrong while processing your request.';

            return ApiHelper::invalidResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/users",
     * summary="view all users",
     * description="show all users",
     * operationId="view all users",
     * tags={"Admin/Manage-users"},
     * security={ {"sanctum": {} }},
     * @OA\Response(
     *    response=401,
     *    description="Returns when user is not authenticated",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Not authorized"),
     *    )
     * )
     * )
     */
    public function getAllUsers()
    {
        try {
            $users = User::all();
            $message = 'users retrieved successfully';

            return response()->json(['data' => $users, 'message' => $message, 'status' => 'SUCCESS', 'status_code' => Response::HTTP_OK], Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
