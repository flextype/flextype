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

    public function find($array)
    {
        $oldErrorReporting = error_reporting();
        error_reporting(0);

        // Create Array Collection from entries array
        $this->collection = new ArrayCollection($array);

        // Create Criteria for filtering Selectable collections.
        $this->criteria = new Criteria();

        return $this;
    }

    public function where($key, $expr, $value)
    {
        $this->criteria->where(new Comparison($key, $this->expression[$expr], $value));

        return $this;
    }

    public function andWhere($key, $expr, $value)
    {
        $this->criteria->andWhere(new Comparison($key, $this->expression[$expr], $value));

        return $this;
    }

    public function orWhere($key, $expr, $value)
    {
        $this->criteria->orWhere(new Comparison($key, $this->expression[$expr], $value));

        return $this;
    }

    public function orderBy($field, $direction)
    {
        $this->criteria->orderBy([$field => $this->direction[$direction]]);

        return $this;
    }

    public function setFirstResult($firstResult)
    {
        $this->criteria->setFirstResult($firstResult);

        return $this;
    }

    public function limit($limit)
    {
        $this->criteria->setMaxResults($limit);

        return $this;
    }

    public function exists() : bool
    {
        return count($this->toArray());
    }

    public function count() : int
    {
        return count($this->toArray());
    }

    public function toArray()
    {
        // Get entries for matching criterias
        $collection = $this->collection->matching($this->criteria);

        // Gets a native PHP array representation of the collection.
        $array = $collection->toArray();

        // Restore error_reporting
        error_reporting($oldErrorReporting);

        return $array;
    }
}
