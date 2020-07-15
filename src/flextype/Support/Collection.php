<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype\Support;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Flextype\Component\Arr\Arr;
use function array_dot;
use function array_filter;
use function array_keys;
use function array_merge;
use function array_rand;
use function array_undot;
use function count;
use function error_reporting;
use function is_null;
use const E_NOTICE;

class Collection
{
    /**
     * Order Direction
     *
     * @var array
     * @access public
     */
    public $direction = [
        'ASC'  => Criteria::ASC,
        'DESC' => Criteria::DESC,
    ];

    /**
     * Expression
     *
     * @var array
     * @access public
     */
    public $expression = [
        'eq' => Comparison::EQ,
        '=' => Comparison::EQ,

        '<>' => Comparison::NEQ,
        '!=' => Comparison::NEQ,
        'neq' => Comparison::NEQ,

        '<' => Comparison::LT,
        'lt' => Comparison::LT,

        '<=' => Comparison::LTE,
        'lte' => Comparison::LTE,

        '>' => Comparison::GT,
        'gt' => Comparison::GT,

        '>=' => Comparison::GTE,
        'gte' => Comparison::GTE,

        'is' => Comparison::IS,
        'in' => Comparison::IN,
        'nin' => Comparison::NIN,
        'contains' => Comparison::CONTAINS,
        'like' => Comparison::CONTAINS,
        'member_of' => Comparison::MEMBER_OF,
        'start_with' => Comparison::STARTS_WITH,
        'ends_with' => Comparison::ENDS_WITH,
    ];

    /**
     * Collection
     *
     * @access private
     */
    private $collection;

    /**
     * Criteria
     *
     * @access private
     */
    private $criteria;

    /**
     * Error Reporting
     *
     * @access private
     */
    private $errorReporting;

    /**
     * Create a new collection.
     *
     * @param  mixed $items
     *
     * @return void
     */
    public function __construct($items)
    {
        // Save error_reporting state and turn it off
        // because PHP Doctrine Collections don't works with collections
        // if there is no requested fields to search inside item:
        //      vendor/doctrine/collections/lib/Doctrine/Common/Collections/Expr/ClosureExpressionVisitor.php
        //      line 40: return $object[$field];
        //
        // @todo research this issue and find possible better solution to avoid this in the future
        $this->errorReporting = error_reporting();
        error_reporting($this->errorReporting & ~E_NOTICE);

        // Check if array is associative
        // Flatten a multi-dimensional array with dots.
        if ($this->isAssocArray($items)) {
            $flat_array = [];

            foreach ($items as $key => $value) {
                $flat_array[$key] = array_dot($value);
            }

            $items = $flat_array;
        }

        // Create Array Collection
        $this->collection = new ArrayCollection($items);

        // Create Criteria for filtering Selectable collections.
        $this->criteria = new Criteria();
    }

    /**
     * Create a collection from the given items.
     *
     * @param  mixed $items Items to collect
     */
    public static function collect($items) : Collection
    {
        return new Collections($items);
    }

    /**
     * Merge the collection with the given items.
     *
     * @param  mixed $items
     *
     * @return static
     */
    public function merge(...$items)
    {
        $this->collection = new ArrayCollection(
            array_merge($this->collection->toArray(), ...$items)
        );

        return $this;
    }

    /**
     * Sets the where expression to evaluate when this Criteria is searched for.
     *
     * @param string $field The field path using dot notation.
     * @param string $expr  Expression @see $this->expression
     * @param mixed  $value Value
     *
     * @return static
     *
     * @access public
     */
    public function where(string $field, string $expr, $value)
    {
        $this->criteria->where(new Comparison($field, $this->expression[$expr], $value));

        return $this;
    }

    /**
     * Appends the where expression to evaluate when this Criteria is searched
     * for using an AND with previous expression.
     *
     * @param string $field The field path using dot notation.
     * @param string $expr  Expression @see $this->expression
     * @param mixed  $value Value
     *
     * @return static
     *
     * @access public
     */
    public function andWhere(string $field, string $expr, $value)
    {
        $this->criteria->andWhere(new Comparison($field, $this->expression[$expr], $value));

        return $this;
    }

    /**
     * Appends the where expression to evaluate when this Criteria is searched
     * for using an OR with previous expression.
     *
     * @param string $field The field path using dot notation.
     * @param string $expr  Expression @see $this->expression
     * @param mixed  $value Value
     *
     * @return static
     *
     * @access public
     */
    public function orWhere(string $field, string $expr, $value)
    {
        $this->criteria->orWhere(new Comparison($field, $this->expression[$expr], $value));

        return $this;
    }

    /**
     * Sets the ordering of the result of this Criteria.
     *
     * Keys are field and values are the order, being either ASC or DESC.
     *
     * @param string $field     The field path using dot notation.
     * @param string $direction Sort direction: asc or desc
     *
     * @return static
     *
     * @access public
     */
    public function orderBy(string $field, string $direction)
    {
        $this->criteria->orderBy([$field => $this->direction[$direction]]);

        return $this;
    }

    /**
     * Set the number of first result that this Criteria should return.
     *
     * @param int|null $firstResult The value to set.
     *
     * @return static
     *
     * @access public
     */
    public function setFirstResult(?int $firstResult)
    {
        $this->criteria->setFirstResult($firstResult);

        return $this;
    }

    /**
     * Sets the max results that this Criteria should return.
     *
     * @param int|null $limit The value to set.
     *
     * @return
     *
     * @access public
     */
    public function limit(?int $limit)
    {
        $this->criteria->setMaxResults($limit);

        return $this;
    }

    /**
     * Returns a value indicating whether the collection contains any item of data.
     *
     * @return bool Return true or false.
     *
     * @access public
     */
    public function exists() : bool
    {
        return $this->count() > 0;
    }

    /**
     * Returns the number of items.
     *
     * @return int The number of items.
     *
     * @access public
     */
    public function count() : int
    {
        return count($this->all());
    }

    /**
     * Returns a last single item of result.
     *
     * @return array Item
     *
     * @access public
     */
    public function last() : array
    {
        return array_undot($this->matchCollection()->last());
    }

    /**
     * Returns a single item of result.
     *
     * Moves the internal iterator position to the next element and returns this element.
     *
     * @return array Item
     *
     * @access public
     */
    public function next() : array
    {
        return array_undot($this->matchCollection()->next());
    }

    /**
     * Returns a single item of result.
     *
     * Moves the internal iterator position to the next element and returns this element.
     *
     * @return array Item
     *
     * @access public
     */
    public function shuffle() : array
    {
        $results = $this->matchCollection()->toArray();

        return Arr::isAssoc($results) ?
                            Arr::shuffle(array_undot(array_dot($results))) :
                            $results;
    }

    /**
     * Returns a single item of result.
     *
     * @return array Item
     *
     * @access public
     */
    public function first() : array
    {
        $results = $this->matchCollection()->first();

        return Arr::isAssoc($results) ?
                            array_undot($results) :
                            $results;
    }

    /**
     * Returns one or a specified number of items randomly from the collection.
     *
     * @return mixed The result data.
     *
     * @access public
     */
    public function random(?int $number = null)
    {
        // Match collection
        $collection = $this->collection->matching($this->criteria);

        // Restore error_reporting
        error_reporting($this->errorReporting);

        // Gets a native PHP array representation of the collection.
        $array = $collection->toArray();

        // Set $requested
        $requested = is_null($number) ? 1 : $number;

        // Results array count
        $count = count($array);

        // If requested items more than items available then return setuped count
        if ($requested > $count) {
            $number = $count;
        }

        if ($this->isAssocArray($array)) {
            $array = array_undot(array_dot($array));
        }

        if ((int) $number === 1 || is_null($number)) {
            return $array[array_rand($array)];
        }

        if ((int) $number === 0) {
            return [];
        }

        $keys = array_rand($array, $number);

        $results = [];

        foreach ((array) $keys as $key) {
            $results[$key] = $array[$key];
        }

        return $results;
    }

    /**
     * Extracts a slice of $length elements starting at position $offset from the Collection.
     *
     * If $length is null it returns all elements from $offset to the end of the Collection.
     * Keys have to be preserved by this method. Calling this method will only return
     * the selected slice and NOT change the elements contained in the collection slice is called on.
     *
     * @param int      $offset Slice begin index.
     * @param int|null $length Length of the slice.
     *
     * @return array The array data.
     *
     * @access public
     */
    public function slice(int $offset = 0, ?int $length = null) : array
    {
        // Match collection
        $collection = $this->collection->matching($this->criteria);

        // Restore error_reporting
        error_reporting($this->errorReporting);

        // Gets a native PHP array representation of the collection.
        $results = $collection->slice($offset, $length);

        // Return results
        return $this->isAssocArray($results) ? array_undot(array_dot($results)) : $results;
    }

    /**
     * Returns all results as an array.
     *
     * @return array The array data.
     *
     * @access public
     */
    public function all() : array
    {
        // Match collection
        $collection = $this->collection->matching($this->criteria);

        // Restore error_reporting
        error_reporting($this->errorReporting);

        // Gets a native PHP array representation of the collection.
        $results = $collection->toArray();

        // Return results
        return $this->isAssocArray($results) ? array_undot(array_dot($results)) : $results;
    }

    /**
     * Returns TRUE if the array is associative and FALSE if not.
     *
     * @param  array $array Array to check
     *
     * @access  public
     */
    protected function isAssocArray(array $array) : bool
    {
        return (bool) count(array_filter(array_keys($array), 'is_string'));
    }
}
