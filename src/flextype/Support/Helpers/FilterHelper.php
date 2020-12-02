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
     * @param  mixed $items  Items.
     * @param  array $params Params array.
     *
     * @return array|bool|int
     */
    function filter($items = [], array $params = [])
    {
        $collection = arrays($items);

        ! isset($params['return']) and $params['return'] = 'all';

        if (isset($params['where'])) {
            if (is_array($params['where'])) {
                foreach ($params['where'] as $key => $value) {
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

        if (isset($params['group_by'])) {
            $collection->groupBy($params['group_by']);
        }

        if (isset($params['slice_offset']) && isset($params['slice_offset'])) {
            $collection->slice(
                isset($params['slice_offset']) ? (int) $params['slice_offset'] : 0,
                isset($params['slice_limit']) ? (int) $params['slice_limit'] : 0
            );
        }

        if (isset($params['sort_by'])) {
            if (isset($params['sort_by']['key']) && isset($params['sort_by']['direction'])) {
                $collection->sortBy($params['sort_by']['key'], $params['sort_by']['direction']);
            }
        }

        if (isset($params['offset'])) {
            $collection->offset(isset($params['offset']) ? (int) $params['offset'] : 0);
        }

        if (isset($params['limit'])) {
            $collection->limit(isset($params['limit']) ? (int) $params['limit'] : 0);
        }

        switch ($params['return']) {
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
                $result = $collection->random(isset($params['random']) ? (int) $params['random'] : null);
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
