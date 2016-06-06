<?php

namespace Statamic\Addons\Rucksack;

use Statamic\API\Str;
use Statamic\Extend\Tags;

class RucksackTags extends Tags
{
    /**
     * @var Rucksack
     */
    private $rucksack;

    /**
     * Initialize
     *
     * @return void
     */
    protected function init()
    {
        $this->rucksack = new Rucksack;
    }

    /**
     * Outputs a boolean whether the rucksack has a given key
     *
     * {{ rucksack:has id="" }}
     *
     * @return bool
     */
    public function has()
    {
        return $this->rucksack->has($this->id());
    }

    /**
     * Output a url to add the item to the rucksack
     *
     * <a href="{{ rucksack:add_url }}">Add</a>
     *
     * @return string
     */
    public function addUrl()
    {
        $url = $this->eventUrl('add/'.$this->id());

        if (! empty($extras = $this->extras())) {
            $url .= '?' . http_build_query($extras);
        }

        return $url;
    }

    /**
     * Output a url to the remove the item to the rucksack
     *
     * <a href="{{ rucksack:remove_url }}">Remove</a>
     *
     * @return string
     */
    public function removeUrl()
    {
        return $this->eventUrl('remove/'.$this->id());
    }

    /**
     * Output either an add or remove url depending on whether the item is in the rucksack
     *
     * <a href="{{ rucksack:toggle_url }}">Toggle</a>
     *
     * @return string
     */
    public function toggleUrl()
    {
        return ($this->has()) ? $this->removeLink() : $this->addLink();
    }

    /**
     * Loop over the contents of the rucksack
     *
     * {{ rucksack:contents }}
     *   ...
     * {{ /rucksack:contents }}
     *
     * @return string
     */
    public function contents()
    {
        $contents = $this->rucksack->contents();

        if ($contents->isEmpty()) {
            return $this->parse(['no_results' => true]);
        }

        // Reformat the data for templating
        $data = collect($contents->values()->toArray())->map(function ($arr) {
            $item = $arr['item'];

            // Store an unmerged copy of the original item where
            // its variables are accessible by {{ item:foo }}
            $item['item'] = $item;

            // Add the 'extras' into the array
            return array_merge($item, $arr['extra']);
        })->all();

        // Insert the items into a scope when using the 'as' parameter
        if ($scope = $this->get('as')) {
            $data = [[ $scope => $data ]];
        }

        return $this->parseLoop($data);
    }

    /**
     * Get the ID for the rucksack tags
     *
     * Either from an ID tag or {{ id }} out of the context
     *
     * @return string
     */
    private function id()
    {
        return $this->get('id', array_get($this->context, 'id'));
    }

    /**
     * Extract any "extra" prefixed parameters
     *
     * @return array
     */
    private function extras()
    {
        return collect($this->parameters)->map(function ($value, $param) {
            return compact('param', 'value');
        })->filter(function ($arr) {
            return Str::startsWith($arr['param'], 'extra:');
        })->map(function ($arr) {
            $arr['param'] = substr($arr['param'], 6);
            return $arr;
        })->pluck('value', 'param')->all();
    }
}
