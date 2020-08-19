<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\App\Support\Collection;

if (! function_exists('collect')) {
    /**
     * Create a collection from the given value.
     *
     * @param  mixed $items Items
     */
    function collect($items) : Collection
    {
        return new Collection($items);
    }
}

if (! function_exists('collect_filter')) {
    /**
     * Create a collection from the given value and filter it.
     *
     * @param  mixed $items  Items
     * @param  array $filter Filters array
     *
     * @return array|bool|int
     */
    function collect_filter($items, array $filter)
    {
        $collection = new Collection($items);

        // Bind: return
        $bind_return = $filter['return'] ?? 'all';

        // Bind: where
        $bind_where = [];
        if (isset($filter['where']['key']) && isset($filter['where']['expr']) && isset($filter['where']['value'])) {
            $bind_where['where']['key']   = $filter['where']['key'];
            $bind_where['where']['expr']  = $filter['where']['expr'];
            $bind_where['where']['value'] = $filter['where']['value'];
        }

        // Bind: and where
        $bind_and_where = [];
        if (isset($filter['and_where'])) {
            foreach ($filter['and_where'] as $key => $value) {
                if (! isset($value['key']) || ! isset($value['expr']) || ! isset($value['value'])) {
                    continue;
                }

                $bind_and_where[$key] = $value;
            }
        }

        // Bind: or where
        $bind_or_where = [];
        if (isset($filter['or_where'])) {
            foreach ($filter['or_where'] as $key => $value) {
                if (! isset($value['key']) || ! isset($value['expr']) || ! isset($value['value'])) {
                    continue;
                }

                $bind_or_where[$key] = $value;
            }
        }

        // Bind: order by
        $bind_order_by = [];
        if (isset($filter['order_by']['field']) && isset($filter['order_by']['direction'])) {
            $bind_order_by['order_by']['field']     = $filter['order_by']['field'];
            $bind_order_by['order_by']['direction'] = $filter['order_by']['direction'];
        }

        // Exec: where
        if (isset($bind_where['where']['key']) && isset($bind_where['where']['expr']) && isset($bind_where['where']['value'])) {
            $collection->where($bind_where['where']['key'], $bind_where['where']['expr'], $bind_where['where']['value']);
        }

        // Exec: and where
        if (isset($bind_and_where)) {
            foreach ($bind_and_where as $key => $value) {
                $collection->andWhere($value['key'], $value['expr'], $value['value']);
            }
        }

        // Exec: or where
        if (isset($bind_or_where)) {
            foreach ($bind_or_where as $key => $value) {
                $collection->orWhere($value['key'], $value['expr'], $value['value']);
            }
        }

        // Exec: order by
        if (isset($bind_order_by['order_by']['field']) && isset($bind_order_by['order_by']['direction'])) {
            $collection->orderBy($bind_order_by['order_by']['field'], $bind_order_by['order_by']['direction']);
        }

        // Exec: only
        if (isset($filter['only'])) {
            $collection->only($filter['only']);
        }

        // Gets a native PHP array representation of the collection.
        switch ($bind_return) {
            case 'first':
                $items = $collection->first();
                break;
            case 'last':
                $items = $collection->last();
                break;
            case 'next':
                $items = $collection->next();
                break;
            case 'random':
                $bind_random_value = isset($filter['random_value']) ? (int) $filter['random_value'] : null;
                $items             = $collection->random($bind_random_value);
                break;
            case 'limit':
                $bind_set_max_result_value = isset($filter['limit_value']) ? (int) $filter['limit_value'] : 0;
                $items                     = $collection->limit($bind_set_max_result_value);
                break;
            case 'set_first_result':
                $bind_set_first_result_value = isset($filter['set_first_result_value']) ? (int) $filter['set_first_result_value'] : 0;
                $items                       = $collection->setFirstResult($bind_set_first_result_value);
                break;
            case 'slice':
                $bind_slice_offset_value = isset($filter['slice_offset_value']) ? (int) $filter['slice_offset_value'] : 0;
                $bind_slice_limit_value  = isset($filter['slice_limit_value']) ? (int) $filter['slice_limit_value'] : 0;
                $items                   = $collection->slice($bind_slice_offset_value, $bind_slice_limit_value);
                break;
            case 'exists':
                $items = $collection->exists();
                break;
            case 'count':
                $items = $collection->count();
                break;
            case 'shuffle':
                $items = $collection->shuffle();
                break;
            case 'all':
                $items = $collection->all();
                break;
            default:
                $items = $collection->all();
                break;
        }

        // Return entries
        return $items;
    }
}
