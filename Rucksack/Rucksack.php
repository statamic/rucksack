<?php

namespace Statamic\Addons\Rucksack;

use Statamic\API\Str;
use Statamic\API\Asset;
use Statamic\API\Content;
use Statamic\Extend\Addon;

class Rucksack extends Addon
{
    /**
     * Add a key to the rucksack
     *
     * @param string $key
     * @param array  $data
     * @return void
     */
    public function add($key, $data)
    {
        $contents = $this->fetchContents();

        $contents->put($key, $data);

        $this->storeContents($contents);
    }

    /**
     * Remove a key from the rucksack
     *
     * @param  string $key
     * @return void
     */
    public function remove($key)
    {
        $contents = $this->fetchContents();

        $contents->forget($key);

        $this->storeContents($contents);
    }

    /**
     * Check if a key exists in the rucksack
     *
     * @param  string  $key
     * @return bool
     */
    public function has($key)
    {
        return $this->fetchContents()->has($key);
    }

    /**
     * Get the contents of the rucksack
     *
     * @return Collection
     */
    public function contents()
    {
        return $this->fetchContents()->map(function ($data, $id) {
            return collect([
                'item' => $this->getDataFromId($id),
                'extra' => $this->formatExtraData($data),
            ]);
        });
    }

    /**
     * Fetch contents from session
     *
     * @return Collection
     */
    private function fetchContents()
    {
        return $this->session->get('contents', collect());
    }

    /**
     * Store contents in session
     *
     * @param  Collection $contents
     * @return void
     */
    private function storeContents($contents)
    {
        $this->session->put('contents', $contents);
    }

    /**
     * Get data (asset or content) by ID
     *
     * @param  string $id
     * @return \Statamic\Contracts\Data\Data
     */
    private function getDataFromId($id)
    {
        if (! $item = Content::uuidRaw($id)) {
            $item = Asset::uuidRaw($id);
        }

        return $item;
    }

    /**
     * Format the extra data
     *
     * Any items ending with `_id` (ie. `project_id`) will be fetched
     * and added to the array without the suffix (ie. `project`).
     *
     * @param  array $data
     * @return Collection
     */
    private function formatExtraData($data)
    {
        $data = collect($data);
        $objects = collect();

        $data->each(function ($value, $key) use ($objects) {
            if (! Str::endsWith($key, '_id')) {
                return;
            }

            $objects->put(Str::removeRight($key, '_id'), $this->getDataFromId($value));
        });

        return $data->merge($objects);
    }
}
