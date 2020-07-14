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
     * Constructor
     *
     * @access public
     */
    public function __construct($array)
    {
        // Save error_reporting state and turn it off
        // because PHP Doctrine Collections don't works with collections
        // if there is no requested fields to search inside item:
        //      vendor/doctrine/collections/lib/Doctrine/Common/Collections/Expr/ClosureExpressionVisitor.php
        //      line 40: return $object[$field];
        //
        // @todo research this issue and find possible better solution to avoid this in the future
        $this->oldErrorReporting = error_reporting();
        error_reporting($this->oldErrorReporting & ~E_NOTICE);

        // Flatten a multi-dimensional array with dots.
        if (Arr::isAssoc($array)) {

            $flat_array = [];

            foreach ($array as $key => $value) {
                $flat_array[$key] = Arr::dot($value);
            }

            $array = $flat_array;
        }

        // Create Array Collection from entries array
        $this->collection = new ArrayCollection($array);

        // Create Criteria for filtering Selectable collections.
        $this->criteria = new Criteria();

        // Return
        return $this;

    }

    public static function collect($array)
    {
        return new Collections($array);
    }

    public function merge(...$arrays)
    {
        $this->collection = new ArrayCollection(
            array_merge($this->collection->toArray(), ...$arrays)
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
     * @return
     *
     * @access public
     */
    public function where($field, $expr, $value)
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
     * @return
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
     * @return
     *
     * @access public
     */
    public function orWhere($field, $expr, $value)
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
     * @return
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
     * @return
     *
     * @access public
     */
    public function setFirstResult($firstResult)
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
    public function limit($limit)
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
        return ($this->count() > 0) ? true : false ;
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
    public function last()
    {
        return Arr::undot($this->matchCollection()->last());
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
        return Arr::undot($this->matchCollection()->next());
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
                            Arr::shuffle(Arr::undot(Arr::dot($results))) :
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
                            Arr::undot($results) :
                            $results;
    }

    /**
     * Returns random item from result.
     *
     * @return array The array data.
     *
     * @access public
     */
    public function random()
    {
        $results = $this->matchCollection()->toArray();

        return Arr::isAssoc($results) ?
                  $results[array_rand(Arr::undot(Arr::dot($results)))] :
                  $results[array_rand($results)];
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
    public function slice(int $offset = 0, int $limit = null) : array
    {
        $results = $this->matchCollection()->slice($offset, $limit);

        return Arr::isAssoc($results) ?
                  Arr::undot(Arr::dot($results)) :
                  $results;
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
        $results = $this->matchCollection()->toArray();

        return Arr::isAssoc($results) ?
                  Arr::undot(Arr::dot($results)) :
                  $results;
    }

    /**
     * Match collection
     *
     * @access protected
     */
    public function matchCollection()
    {
        // Match collection
        $collection = $this->collection->matching($this->criteria);

        // Restore error_reporting
        error_reporting($this->oldErrorReporting);

        // Return collection
        return $collection;
    }
}
