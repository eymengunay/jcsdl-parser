<?php

/*
 * This file is part of the Jcsdl package.
 *
 * (c) 2014 Eymen Gunay <eymen@egunay.com>
 */

namespace Eo\Jcsdl;

/**
 * Filter
 */
class Filter
{
    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $field;

    /**
     * @var string
     */
    protected $operator;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * To array
     * 
     * @return array
     */
    public function toArray()
    {
        return array(
            'id'       => $this->getId(),
            'field'    => $this->getField(),
            'operator' => $this->getOperator(),
            'value'    => $this->getValue()
        );
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
     * Set field
     * 
     * @param  string $field
     * @return self
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get field
     * 
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set operator
     * 
     * @param  string $operator
     * @return self
     */
    public function setOperator($operator)
    {
        $this->operator = $operator;

        return $this;
    }

    /**
     * Get operator
     * 
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     * Set value
     * 
     * @param  mixed $value
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     * 
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}