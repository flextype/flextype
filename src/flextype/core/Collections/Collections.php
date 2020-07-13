<?php

declare(strict_types=1);

/**
 * Flextype (http://flextype.org)
 * Founded by Sergey Romanenko and maintained by Flextype Community.
 */

namespace Flextype;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Expr\Comparison;
use Flextype\Component\Filesystem\Filesystem;
use Flextype\Component\Arr\Arr;

class Collections
{
    /**
     * Flextype Dependency Container
     *
     * @access private
     */
    private $flextype;

    /**
     * Entires Order Direction
     *
     * @var array
     * @access public
     */
    public $direction = [
        'asc' => Criteria::ASC,
        'desc' => Criteria::DESC,
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
     * data array
     *
     * @var array
     * @access public
     */
    public $array = [];

    /**
     * Constructor
     *
     * @access public
     */
    public function __construct($flextype)
    {
        $this->flextype = $flextype;
    }

    /**
     * Find
     *
     * @param string $array Array
     *
     * @return static self reference
     *
     * @access public
     */
    public function find($array)
    {
        // Save error_reporting state and turn it off
        // because PHP Doctrine Collections don't works with collections
        // if there is no requested fields to search:
        //      vendor/doctrine/collections/lib/Doctrine/Common/Collections/Expr/ClosureExpressionVisitor.php
        //      line 40: return $object[$field];
        //
        // @todo research this issue and find possible better solution to avoid this in the future
        $oldErrorReporting = error_reporting();
        error_reporting(0);

        // Flatten a multi-dimensional array with dots.
        $flat_array = [];
        foreach ($array as $key => $value) {
            $flat_array[$key] = Arr::dot($value);
        }

        // Create Array Collection from entries array
        $this->collection = new ArrayCollection($flat_array);

        // Create Criteria for filtering Selectable collections.
        $this->criteria = new Criteria();

        // Return
        return $this;
    }


    /**
     * Sets the where expression to evaluate when this Criteria is searched for.
     *
     * @param string $field The field path using dot notation.
     * @param string $expr  Expression @see $this->expression
     * @param mixed  $value Value
     *
     * @return static self reference
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
     * @return static self reference
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
     * @return static self reference
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
     * @return static self reference
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
     * @return static self reference
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
     * @return static self reference
     *
     * @access public
     */
    public function limit($limit)
    {
        $this->criteria->setMaxResults($limit);

        return $this;
    }

    /**
     * Returns the number of items.
     *
     * @return int The number of items.
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

    public function last()
    {
        return Arr::undot(Arr::dot($this->matchCollection()->last()));
    }

    public function one()
    {
        return Arr::undot(Arr::dot($this->matchCollection()->first()));
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
        return Arr::undot(Arr::dot($this->matchCollection()->toArray()));
    }

    protected function matchCollection()
    {
        // Match collection
        $collection = $this->collection->matching($this->criteria);

        // Restore error_reporting
        error_reporting($oldErrorReporting);

        // Return collection
        return $collection;
    }
}
