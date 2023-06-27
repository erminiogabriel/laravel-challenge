<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceRequest;
use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PlaceController extends Controller
{
    /**
     * Retrieves one or more resources from the server, 
     * applies pagination, and provides the option 
     * to filter the resources based on their names.
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Place::query();

        $name = $request->query('name');

        if ($name) {
            $query->where('name', 'ILIKE', '%' . $name . '%');
        }
        $response = $query->paginate(15);
        return response()->json($response, 200);
    }
    /**
     * Retrieves one resources from the server.
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
     * Creates a new resource on the server.
     * @param \App\Http\Requests\PlaceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(PlaceRequest $request)
    {
        $validated = $request->validated();

        $slug = Str::slug($validated["name"] . "-" . $validated["city"] . "-" . $validated["state"], '-');
        $validated['slug'] = $slug;

        if (Place::where('slug', $slug)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'A record with the same slug already exists.',
            ], 409);
        }

        $place = Place::create($validated);

        return response()->json([
            'success' => true,
            'data' => $place,
        ], 201);
    }

    /**
     * Summary of update
     * @param \App\Http\Requests\PlaceRequest $request
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(PlaceRequest $request, $id)
    {

        $place = Place::findOrFail($id);

        $validated = $request->validated();

        $slug = Str::slug($validated["name"] . "-" . $validated["city"] . "-" . $validated["state"], '-');
        $validated['slug'] = $slug;

        if (Place::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'A record with the same slug already exists.',
            ], 409);
        }

        $place->update($validated);

        return response()->json([
            'success' => true,
            'data' => $place,
        ], 200);
    }

    /**
     * Summary of destroy
     * @param mixed $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $place = Place::findOrFail($id);
        $place->delete();

        return response()->noContent(204);
    }
}