<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Atomastic\Arrays\Arrays;

if (! function_exists('arrays_filter')) {
    /**
     * Create a collection from the given value and filter it.
     *
     * @param  mixed $items  Items.
     * @param  array $filter Filters array.
     *
     * @return array|bool|int
     */
    function arrays_filter($items = [], array $filter = [])
    {
        $collection = arrays($items);

        if (isset($filter['where'])) {
            if (is_array($filter['where'])) {
                foreach ($filter['where'] as $key => $value) {
                    if (isset($value['key']) &&
                        isset($value['operator']) &&
                        isset($value['value'])) {
                        $collection->where($value['key'], $value['operator'], $value['value']);
                    }
                }
            }
        }

        if (isset($filter['group_by'])) {
            $collection->groupBy($filter['group_by']);
        }

        if (isset($filter['slice_offset']) && isset($filter['slice_offset'])) {
            $collection->slice(isset($filter['slice_offset']) ? (int) $filter['slice_offset'] : 0,
                               isset($filter['slice_limit']) ? (int) $filter['slice_limit'] : 0);
        }

        if (isset($filter['sort_by'])) {
            if (isset($filter['sort_by']['key']) && isset($filter['sort_by']['direction'])) {
                $collection->sortBySubKey($filter['sort_by']['key'], $filter['sort_by']['direction']);
            }
        }

        if (isset($filter['offset'])) {
            $collection->offset(isset($filter['offset']) ? (int) $filter['offset'] : 0);
        }

        if (isset($filter['limit'])) {
            $collection->limit(isset($filter['limit']) ? (int) $filter['limit'] : 0);
        }

        switch ($filter['return']) {
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
                $result = $collection->random(isset($filter['random']) ? (int) $filter['random'] : null);
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
