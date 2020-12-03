<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

if (! function_exists('filter')) {
    /**
     * Create a collection from the given value and filter it.
     *
     * @param  mixed $items   Items.
     * @param  array $options Options array.
     *
     * @return array|bool|int
     */
    function filter($items = [], array $options = [])
    {
        $collection = arrays($items);

        ! isset($options['return']) and $options['return'] = 'all';

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

        if (isset($options['slice_offset']) && isset($options['slice_offset'])) {
            $collection->slice(
                isset($options['slice_offset']) ? (int) $options['slice_offset'] : 0,
                isset($options['slice_limit']) ? (int) $options['slice_limit'] : 0
            );
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
            case 'exists':
                $result = $collection->count() > 0;
                break;
            case 'count':
                $result = $collection->count();
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
