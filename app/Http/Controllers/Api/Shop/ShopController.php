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
            if (! $shop) {
                return ApiHelper::invalidResponse('Shop not found', Response::HTTP_NOT_FOUND);
            }

            return ApiHelper::validResponse('Shops retrieved successfully', ShopResource::collection($shops), Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/shop/{id}",
     * summary="view a shop",
     * description="shop user and invalidate token",
     * operationId="view shop by Id",
     * tags={"Shop"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="ID of shop to return",
     *    in="path",
     *    name="id",
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
    public function getShop($id)
    {
        try {
            $shop = Shop::where('user_id', $this->apiUser()->id)
                ->where('id', $id)->first();

            if (! $shop) {
                return ApiHelper::invalidResponse('Shop not found', Response::HTTP_NOT_FOUND);
            }

            return ApiHelper::validResponse('Shop retrieved successfully', ShopResource::make($shop), Response::HTTP_OK);
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

            return ApiHelper::validResponse('shop created successfully', CreateShopResource::make($shop), Response::HTTP_CREATED);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse('Something went wrong while processing your request.', Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }

    /**
     * @OA\Put(
     * path="/api/v1/update-shop/{id}",
     * summary="update shop",
     * description="shop user and invalidate token",
     * operationId="update shop",
     * tags={"Shop"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="ID of shop to return",
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
    public function updateShop(ShopRequest $request, $id)
    {
        // use validator to validate the request
        try {
            $shop = Shop::where('user_id', $this->apiUser()->id)
                ->where('id', $id)->first();
            if (! $shop) {
                return ApiHelper::invalidResponse('Shop not found', Response::HTTP_NOT_FOUND);
            }
            // update shop
            $shop->update([
                'shop_name' => $request->shop_name,
                'shop_description' => $request->shop_description,
                'shop_address' => $request->shop_address,
                'opening_time' => $request->opening_time,
                'closing_time' => $request->closing_time,
            ]);

            return ApiHelper::validResponse('shop updated successfully', CreateShopResource::make($shop), 200);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse('Something went wrong while processing your request.', Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }

    /**
     * @OA\Delete(
     * path="/api/v1/delete-shop/{id}",
     * summary="delete shop",
     * description="shop user and invalidate token",
     * operationId="delete shop",
     * tags={"Shop"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="ID of shop to delete",
     *    in="path",
     *    name="id",
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
    public function deleteShop(Request $request, $id)
    {
        try {
            $shop = Shop::where('user_id', $this->apiUser()->id)
                ->where('id', $id)->first();

            if (! $shop) {
                return ApiHelper::invalidResponse('Shop not found', Response::HTTP_NOT_FOUND);
            }

            $shop->delete();

            return ApiHelper::validResponse('shop deleted successfully', Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse('Something went wrong while processing your request.', Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }
}
