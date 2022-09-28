<?php

namespace App\Http\Controllers\Api\Admin;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShopRequest;
use App\Http\Resources\CreateShopResource;
use App\Http\Resources\ShopResource;
use App\Models\Shop;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ManageShopController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $shops = Shop::all();
            $message = 'shops retrieved successfully';

            return ApiHelper::validResponse($message, ShopResource::collection($shops), Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ShopRequest $request)
    {
        // create shop
        try {
            $data = $request->validated();
            $data['user_id'] = auth('api')->user()->id;

            $shop = Shop::create($data);

            $message = 'shop created successfully';

            return ApiHelper::validResponse($message, new CreateShopResource($shop), Response::HTTP_CREATED);
        } catch (Exception $e) {
            $message = 'Something went wrong while processing your request.';

            return ApiHelper::invalidResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $shop = Shop::find($id);
            $message = 'shop retrieved successfully';

            return response()->json(['data' => new ShopResource($shop), 'message' => $message, 'status' => 'SUCCESS', 'status_code' => Response::HTTP_OK], Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            // validator
            $validator = Validator::make($request->all(), [
                'shop_name' => 'required|string|max:255',
                'shop_description' => 'nullable|string|max:255',
                'shop_address' => 'required|string|max:255',
                'opening_time' => 'required|date_format:H:i',
                'closing_time' => 'required|date_format:H:i|after:opening_time',
            ]);

            // if validation fails
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $shop = Shop::find($id);
            $shop->update([
                'shop_name' => $request->shop_name,
                'shop_description' => $request->shop_description,
                'shop_address' => $request->shop_address,
                'opening_time' => $request->opening_time,
                'closing_time' => $request->closing_time,
                'user_id' => auth('api')->user()->id,
            ]);
            $message = 'shop updated successfully';

            return response()->json(['data' => new CreateShopResource($shop), 'message' => $message, 'status' => 'SUCCESS'], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            $message = 'The given data was invalid.';

            return ApiHelper::inputErrorResponse($message, Response::HTTP_UNPROCESSABLE_ENTITY, $request, $e);
        } catch (Exception $e) {
            $message = 'Something went wrong while processing your request.';

            return ApiHelper::invalidResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $shop = Shop::find($id);
            $shop->delete();
            $message = 'shop deleted successfully';

            return response()->json(['message' => $message, 'status' => 'SUCCESS', 'status_code' => Response::HTTP_OK], Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    // approve shop status
    public function updateShopStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:Approved,Declined',
            ]);

            // if validation fails
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            $shop = Shop::find($id);
            $shop->status = $request->status;
            $shop->save();
            $message = 'shop status updated successfully';

            return response()->json(['data' => new CreateShopResource($shop), 'message' => $message, 'status' => 'SUCCESS'], Response::HTTP_CREATED);
        } catch (ValidationException $e) {
            $message = 'The given data was invalid.';

            return ApiHelper::inputErrorResponse($message, Response::HTTP_UNPROCESSABLE_ENTITY, $request, $e);
        } catch (Exception $e) {
            $message = 'Something went wrong while processing your request.';

            return ApiHelper::invalidResponse($message, Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }
}
