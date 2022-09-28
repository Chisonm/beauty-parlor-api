<?php

namespace App\Http\Controllers\Api\Appointment;

use App\Helpers\ApiHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AppointmentRequest;
use App\Http\Requests\UpdateAppointmentStatusRequest;
use App\Http\Resources\AppointmentHistoryResource;
use App\Http\Resources\AppointmentResource;
use App\Http\Resources\CreateAppointmentResource;
use App\Models\Appointment;
use App\Models\AppointmentHistory;
use Exception;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class AppointmentController extends Controller
{
    /**
     * @OA\Get(
     * path="/api/v1/appointments",
     * summary="View Appointment",
     * description="Appointment user and invalidate token",
     * operationId="Appointment",
     * tags={"Appointment"},
     * security={ {"sanctum": {} }},
     * @OA\AcceptHeader(
     *   mediaType="application/json",
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
    public function getAllAppointment()
    {
        // get all appointment
        try {
            $appointments = Appointment::all();

            return ApiHelper::validResponse('appointments retrieved successfully', AppointmentResource::collection($appointments), Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Post(
     * path="/api/v1/appointments",
     * summary="Create Appointment",
     * description="Appointment user and invalidate token",
     * operationId="Create Appointment",
     * tags={"Appointment"},
     * security={ {"sanctum": {} }},
     * @OA\RequestBody(
     *    required=true,
     *    description="fill the",
     *    @OA\JsonContent(
     *       required={"date","time"},
     *       @OA\Property(property="date", type="string", format="date", example="2021-05-01"),
     *       @OA\Property(property="time", type="string", format="time", example="11:00"),
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
    public function createAppointment(AppointmentRequest $request)
    {
        // create appointment
        DB::beginTransaction();
        try {
            // validated data
            $data = $request->validated();
            // dd($data);
            $data['date'] = date('Y-m-d', strtotime($data['date']));
            $data['user_id'] = $this->apiUser()->id;

            $appointment = Appointment::create($data);

            // update appointment history
            AppointmentHistory::create([
                'appointment_id' => $appointment->id,
                'user_id' => $appointment->user_id,
            ]);

            DB::commit();

            return ApiHelper::validResponse('appointment created successfully', CreateAppointmentResource::make($appointment), Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();

            return ApiHelper::invalidResponse('Something went wrong while processing your request.', Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/appointment-history",
     * summary="View Appointment History",
     * description="Appointment user and invalidate token",
     * operationId="View Appointment History",
     * tags={"Appointment"},
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
    public function getAppointmentHistory()
    {
        // get all appointment
        try {
            $appointments = AppointmentHistory::all();

            return ApiHelper::validResponse('appointment history retrieved successfully', AppointmentHistoryResource::collection($appointments), Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/appointment/{id}",
     * summary="View Appointment by id",
     * description="Appointment user and invalidate token",
     * operationId="View Appointment by id",
     * tags={"Appointment"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="ID of Appointment to return",
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
    public function getAppointmentById($id)
    {
        try {
            $appointment = Appointment::find($id);
            if (! $appointment || $appointment->user_id !== $this->apiUser()->id) {
                return ApiHelper::dataNotFound();
            }

            return ApiHelper::validResponse('appointment retrieved successfully', AppointmentResource::make($appointment), Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/appointment-history/{id}",
     * summary="View Appointment History by id",
     * description="Appointment user and invalidate token",
     * operationId="View Appointment History by id",
     * tags={"Appointment"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="ID of Appointment to return",
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
    public function getAppointmentHistoryById($id)
    {
        try {
            $appointment = AppointmentHistory::find($id);

            if (! $appointment || $appointment->user_id !== $this->apiUser()->id) {
                return ApiHelper::dataNotFound();
            }

            return ApiHelper::validResponse('appointment history retrieved successfully', AppointmentHistoryResource::make($appointment), Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Get(
     * path="/api/v1/appointment-user-history/{id}",
     * summary="View Appointment History by user id",
     * description="Appointment user and invalidate token",
     * operationId="View Appointment History by user id",
     * tags={"Appointment"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="ID of Appointment history to return",
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
    public function getAppointmentHistoryByUserId($id)
    {
        // get all appointment
        try {
            $appointment = AppointmentHistory::where('user_id', $id)->get();
            if (! $appointment) {
                return ApiHelper::dataNotFound();
            }

            return ApiHelper::validResponse('user appointment history retrieved successfully', AppointmentHistoryResource::collection($appointment), Response::HTTP_OK);
        } catch (Exception $e) {
            return ApiHelper::invalidResponse($e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @OA\Put(
     * path="/api/v1/appointment/{id}",
     * summary="update appointment status",
     * description="Appointment user and invalidate token",
     * operationId="update appointment status",
     * tags={"Appointment"},
     * security={ {"sanctum": {} }},
     * @OA\Parameter(
     *    description="ID of Appointment history to return",
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
     *    @OA\JsonContent(
     *       required={"status"},
     *       @OA\Property(property="status", type="string", example="Approved"),
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
    public function updateAppointmentStatus(UpdateAppointmentStatusRequest $request, $id)
    {
        try {
            // validated data
            $data = $request->validated();

            $appointment = Appointment::find($id);
            if (! $appointment || $appointment->user_id !== $this->apiUser()->id) {
                return ApiHelper::dataNotFound();
            }

            $appointment->status = $data['status'];
            $appointment->save();

            $message = 'appointment status updated successfully';

            return ApiHelper::validResponse('appointment status updated successfully', CreateAppointmentResource::make($appointment), Response::HTTP_OK);
        } catch (\Exception $e) {
            return ApiHelper::invalidResponse('Something went wrong while processing your request.', Response::HTTP_INTERNAL_SERVER_ERROR, $request, $e);
        }
    }
}
