<?php

namespace App\Services;

use App\Exceptions\DuplicateRecordException;
use App\Models\Place;
use Illuminate\Support\Str;

class PlaceService
{

    /**
     * Retrieves one or more resources from the server, 
     * applies pagination, and provides the option 
     * to filter the resources based on their names.
     * @param mixed $name
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function indexPlaces($name = false)
    {
        $query = Place::query();

        if ($name) {
            $query->where('name', 'ILIKE', '%' . $name . '%');
        }
        return $query->paginate(15);
    }

    /**
     * Creates a new resource on the server
     * @param mixed $data
     * @throws \App\Exceptions\DuplicateRecordException
     * @return \App\Models\Place
     */
    public function createPlace($data): Place
    {
        $slug = Str::slug($data["name"] . "-" . $data["city"] . "-" . $data["state"], '-');
        $data['slug'] = $slug;

        if (Place::where('slug', $slug)->exists()) {
            throw new DuplicateRecordException('A record with the same slug already exists.');
        }

        $place = Place::create($data);

        return $place;
    }

    /**
     * Updates a resource on the server
     * @param mixed $id
     * @param mixed $data
     * @throws \App\Exceptions\DuplicateRecordException
     * @return \App\Models\Place
     */
    public function updatePlace($id, $data): Place
    {
        $place = Place::findOrFail($id);

        $slug = Str::slug($data["name"] . "-" . $data["city"] . "-" . $data["state"], '-');
        $data['slug'] = $slug;

        if (Place::where('slug', $slug)->where('id', '!=', $id)->exists()) {
            throw new DuplicateRecordException('A record with the same slug already exists.');
        }

        $place->update($data);

        return $place;
    }

    /**
     * Deletes a resource on the server
     * @param mixed $id
     * @return void
     */
    public function deletePlace($id): void
    {
        $place = Place::findOrFail($id);
        $place->delete();
    }
}