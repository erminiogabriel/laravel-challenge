<?php

namespace App\Http\Controllers;

use App\Exceptions\DuplicateRecordException;
use App\Http\Requests\PlaceRequest;
use App\Models\Place;
use App\Services\PlaceService;
use Illuminate\Http\Request;

class PlaceController extends Controller
{
    private $placeService;

    /**
     * __construct
     * @param \App\Services\PlaceService $placeService
     */
    public function __construct(PlaceService $placeService)
    {
        $this->placeService = $placeService;
    }

    /**
     * Retrieves one or more resources from the server, 
     * applies pagination, and provides the option 
     * to filter the resources based on their names.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $response = $this->placeService->indexPlaces($request->query('name'));
        return response()->json($response, 200);
    }
    /**
     * Retrieves one resources from the server
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $place = Place::findOrFail($id);

        return response()->json([
            'success' => true,
            "data" => $place
        ], 200);
    }

    /**
     * Creates a new resource on the server
     * @param \App\Http\Requests\PlaceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(PlaceRequest $request)
    {
        try {
            $place = $this->placeService->createPlace($request->validated());

            return response()->json([
                'success' => true,
                'data' => $place,
            ], 201);
        } catch (DuplicateRecordException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    /**
     * Updates a resource on the server
     * @param \App\Http\Requests\PlaceRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PlaceRequest $request, $id)
    {
        try {
            $updatedPlace = $this->placeService->updatePlace($id, $request->validated());

            return response()->json([
                'success' => true,
                'data' => $updatedPlace,
            ], 200);
        } catch (DuplicateRecordException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 409);
        }
    }

    /**
     * Deletes a resource on the server
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->placeService->deletePlace($id);
        return response()->noContent(204);
    }
}