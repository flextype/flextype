<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

use Flextype\Support\Collection;

if (! function_exists('collect')) {
    /**
     * Create a collection from the given value.
     *
     * @param  mixed $value
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
     * @param  mixed $value
     */
    function collect_filter($items, array $filter) : array
    {
        $collection = new Collection($items);

        // Set Expression
        $expression = $collection->expression;

        // Set Direction
        $direction = $collection->direction;

        // Bind: set first result
        $bind_set_first_result_value = $filter['set_first_result'] ?? 0;

        // Bind: set max result
        $bind_set_max_result_value = $filter['limit'] ?? 0;

        // Bind: return
        $bind_return = $filter['return'] ?? 'all';

        // Bind: random
        $bind_random_value = $filter['random'] ?? 1;

        // Bind: where
        $bind_where = [];
        if (isset($filter['where']['key']) && isset($filter['where']['expr']) && isset($filter['where']['value'])) {
            $bind_where['where']['key']   = $filter['where']['key'];
            $bind_where['where']['expr']  = $expression[$filter['where']['expr']];
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
                $collection->andWhere($value['where']['key'], $value['where']['expr'], $value['where']['value']);
            }
        }

        // Exec: or where
        if (isset($bind_or_where)) {
            foreach ($bind_or_where as $key => $value) {
                $collection->orWhere($value['where']['key'], $value['where']['expr'], $value['where']['value']);
            }
        }

        // Exec: order by
        if (isset($bind_order_by['order_by']['field']) && isset($bind_order_by['order_by']['direction'])) {
            $collection->orderBy($bind_order_by['order_by']['field'], $direction[$bind_order_by['order_by']['direction']]);
        }

        // Exec: set max result
        if ($bind_set_max_result) {
            $collection->limit($bind_set_max_result);
        }

        // Exec: set first result
        if ($bind_set_first_result) {
            $collection->setFirstResult($bind_set_first_result);
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
                $items = $collection->random($bind_random_value);
                break;
            case 'limit':
                $items = $collection->limit($bind_set_max_result_value);
                break;
            case 'set_first_result':
                $items = $collection->setFirstResult($bind_set_first_result_value);
                break;
            case 'shuffle':
                $items = $collection->shuffle();
                break;
            default:
                $items = $collection->all();
                break;
        }

        // Return entries
        return $items;
    }
}
