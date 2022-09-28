<?php

namespace App\Http\Controllers\Api\Shop;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShopRequest;
use App\Http\Resources\CreateShopResource;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ShopController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/v1/shops",
     * summary="view shop",
     * description="shop user and invalidate token",
     * operationId="shop",
     * tags={"Shop"},
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
    public function getAllShop()
    {
        try {
            $shops = Shop::where('user_id', $this->apiUser()->id)->get();

            return ApiHelper::validResponse('Shops retrieved successfully', ShopResource::collection($shops), 200);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/shops/{shop}",
     * summary="view a shop",
     * description="shop user and invalidate token",
     * operationId="view shop by Id",
     * tags={"Shop"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="ID of shop to return",
     *    in="path",
     *    name="shop",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
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
    public function getShop(Shop $shop)
    {
        try {
            if (! $shop || $shop->user_id !== $this->apiUser()->id) {
                return ApiHelper::dataNotFound();
            }

            return ApiHelper::validResponse('Shop retrieved successfully', ShopResource::make($shop), 200);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     * path="/api/v1/create-shop",
     * summary="create shop",
     * description="shop user and invalidate token",
     * operationId="create shop",
     * tags={"Shop"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="fill the",
     *    @OA\JsonContent(
     *       required={"shop_name","shop_description", "shop_address", "opening_time", "closing_time"},
     *       @OA\Property(property="shop_name", type="string", format="string", example="test shop"),
     *       @OA\Property(property="shop_description", type="string", format="string", example="test description"),
     *       @OA\Property(property="shop_address", type="string", format="string", example="test address"),
     *       @OA\Property(property="opening_time", type="string", format="password", example="8:00"),
     *       @OA\Property(property="closing_time", type="string", format="password", example="10:00"),
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
    public function createShop(ShopRequest $request)
    {
        // use validator to validate the request
        try {
            // create shop
            $shop = Shop::create([
                'shop_name' => $request->shop_name,
                'shop_description' => $request->shop_description,
                'shop_address' => $request->shop_address,
                'opening_time' => $request->opening_time,
                'closing_time' => $request->closing_time,
                'user_id' => $this->apiUser()->id,
            ]);

            $message = 'shop created successfully';

            return ApiHelper::validResponse($message, CreateShopResource::make($shop), 201);
        } catch (Exception $e) {
            $message = 'Something went wrong while processing your request.';

            return ApiHelper::invalidResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }

    /**
     * @OA\Put(
     * path="/api/v1/update-shop/{shop}",
     * summary="update shop",
     * description="shop user and invalidate token",
     * operationId="update shop",
     * tags={"Shop"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="ID of shop to return",
     *    in="path",
     *    name="shop",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
     * ),
     * @OA\RequestBody(
     *    required=true,
     *    description="fill the",
     *    @OA\JsonContent(
     *       required={"shop_name","shop_description", "shop_address", "opening_time", "closing_time"},
     *       @OA\Property(property="shop_name", type="string", format="string", example="test shop"),
     *       @OA\Property(property="shop_description", type="string", format="string", example="test description"),
     *       @OA\Property(property="shop_address", type="string", format="string", example="test address"),
     *       @OA\Property(property="opening_time", type="string", format="password", example="8:00"),
     *       @OA\Property(property="closing_time", type="string", format="password", example="10:00"),
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
    public function updateShop(ShopRequest $request, Shop $shop)
    {
        // use validator to validate the request
        try {
            if (! $shop || $shop->user_id !== $this->apiUser()->id) {
                return ApiHelper::dataNotFound();
            }

            // update shop
            $shop->update([
                'shop_name' => $request->shop_name,
                'shop_description' => $request->shop_description,
                'shop_address' => $request->shop_address,
                'opening_time' => $request->opening_time,
                'closing_time' => $request->closing_time,
            ]);

            $message = 'shop updated successfully';

            return ApiHelper::validResponse($message, CreateShopResource::make($shop), 200);
        } catch (Exception $e) {
            $message = 'Something went wrong while processing your request.';

            return ApiHelper::invalidResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/v1/delete-shop/{shop}",
     * summary="delete shop",
     * description="shop user and invalidate token",
     * operationId="delete shop",
     * tags={"Shop"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="ID of shop to delete",
     *    in="path",
     *    name="shop",
     *    required=true,
     *    example="1",
     *    @OA\Schema(
     *       type="integer",
     *       format="int64"
     *    )
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
    public function deleteShop(Request $request, Shop $shop)
    {
        try {
            if (! $shop || $shop->user_id !== $this->apiUser()->id) {
                return ApiHelper::dataNotFound();
            }

            $shop->delete();

            $message = 'shop deleted successfully';

            return ApiHelper::validResponse($message);
        } catch (Exception $e) {
            $message = 'Something went wrong while processing your request.';

            return ApiHelper::invalidResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }
}
