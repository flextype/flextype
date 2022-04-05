<?php 

declare(strict_types=1);

use Glowy\Arrays\Arrays as Collection;

if (! function_exists('collection')) {
    /**
     * Create a new arrayable collection object from the given elements.
     *
     * Initializes a Collection object and assigns $items the supplied values.
     *
     * @param  mixed $items Items
     *
     * @return Collection
     */
    function collection($items = null): Collection
    {
        return Collection::create($items);
    }
}

if (! function_exists('collectionFromJson')) {
    /**
     * Create a new arrayable collection object from the given JSON string.
     *
     * @param string $input A string containing JSON.
     * @param bool   $assoc Decode assoc. When TRUE, returned objects will be converted into associative array collection.
     * @param int    $depth Decode Depth. Set the maximum depth. Must be greater than zero.
     * @param int    $flags Bitmask consisting of decode options
     *
     * @return Collection
     */
    function collectionFromJson(string $input, bool $assoc = true, int $depth = 512, int $flags = 0): Collection
    {
        return Collection::createFromJson($input, $assoc, $depth, $flags);
    }
}

if (! function_exists('collectionFromString')) {
    /**
     * Create a new arrayable collection object from the given string.
     *
     * @param string $string    Input string.
     * @param string $separator Elements separator.
     *
     * @return Collection
     */
    function collectionFromString(string $string, string $separator): Collection
    {
        return Collection::createFromString($string, $separator);
    }
}

if (! function_exists('collectionWithRange')) {
    /**
     * Create a new arrayable object with a range of elements.
     *
     * @param float|int|string $low  First value of the sequence.
     * @param float|int|string $high The sequence is ended upon reaching the end value.
     * @param int              $step If a step value is given, it will be used as the increment between elements in the sequence.
     *                               step should be given as a positive number. If not specified, step will default to 1.
     *
     * @return Collection
     */
    function collectionWithRange($low, $high, int $step = 1): Arrays
    {
        return Collection::createWithRange($low, $high, $step);
    }
}

if (! function_exists('filterCollection')) {
    /**
     * Filter collection.
     *
     * @param  mixed $items   Items.
     * @param  array $options Options array.
     *
     * @return array
     */
    function filterCollection($items = [], array $options = []): array
    {
        $collection = collection($items);

        ! isset($options['return']) and $options['return'] = 'all';

        if (isset($options['only'])) {
            $collection->only($options['only']);
        }

        if (isset($options['except'])) {
            $collection->except($options['except']);
        }

        if (isset($options['where'])) {
            if (is_array($options['where'])) {
                foreach ($options['where'] as $key => $value) {
                    if (
                        ! isset($value['key']) ||
                        ! isset($value['operator']) ||
                        ! isset($value['value'])
                    ) {
                        continue;
                    }

                    $collection->where($value['key'], $value['operator'], $value['value']);
                }
            }
        }

        if (isset($options['group_by'])) {
            $collection->groupBy($options['group_by']);
        }

        if (isset($options['sort_by'])) {
            if (isset($options['sort_by']['key']) && isset($options['sort_by']['direction'])) {
                $collection->sortBy($options['sort_by']['key'], $options['sort_by']['direction']);
            }
        }

        if (isset($options['offset'])) {
            $collection->offset(isset($options['offset']) ? (int) $options['offset'] : 0);
        }

        if (isset($options['limit'])) {
            $collection->limit(isset($options['limit']) ? (int) $options['limit'] : 0);
        }

        switch ($options['return']) {
            case 'first':
                $result = $collection->first();
                break;
            case 'last':
                $result = $collection->last();
                break;
            case 'next':
                $result = $collection->next();
                break;
            case 'random':
                $result = $collection->random(isset($options['random']) ? (int) $options['random'] : null);
                break;
            case 'shuffle':
                $result = $collection->shuffle()->toArray();
                break;
            case 'all':
            default:
                $result = $collection->all();
                break;
        }

        return $result;
    }
}