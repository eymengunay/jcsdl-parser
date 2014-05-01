<?php

/*
 * This file is part of the Jcsdl package.
 *
 * (c) 2014 Eymen Gunay <eymen@egunay.com>
 */

namespace Eo\Jcsdl;

use Eo\Jcsdl\Filter;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Query
 */
class Query
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $logic;

    /**
     * @var array
     */
    protected $filters;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->filters = new ArrayCollection();
    }

    /**
     * To array
     * 
     * @return array
     */
    public function toArray()
    {
        $array = array(
            'id'      => $this->getId(),
            'logic'   => $this->getLogic(),
            'filters' => array()
        );

        foreach ($this->getFilters() as $filter) {
            $array['filters'][] = $filter->toArray();
        }

        return $array;
    }

    /**
     * Set id
     * 
     * @param  string $id
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     * 
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set logic
     * 
     * @param  string $logic
     * @return self
     */
    public function setLogic($logic)
    {
        $this->logic = $logic;

        return $this;
    }

    /**
     * Get logic
     * 
     * @return string
     */
    public function getLogic()
    {
        return $this->logic;
    }

    /**
     * Add filter
     * 
     * @param Filter $filter
     */
    public function addFilter(Filter $filter)
    {
        $this->filters->add($filter);
    }

    /**
     * Get filters
     * 
     * @return ArrayCollection
     */
    public function getFilters()
    {
        return $this->filters;
    }
}