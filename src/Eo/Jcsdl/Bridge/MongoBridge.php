<?php

/*
 * This file is part of the Jcsdl package.
 *
 * (c) 2014 Eymen Gunay <eymen@egunay.com>
 */

namespace Eo\Jcsdl\Bridge;

use Eo\Jcsdl\Query as JcsdlQuery;
use Eo\Jcsdl\Filter;
use Eo\Jcsdl\Exception\JcsdlException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * MongoBridge
 */
class MongoBridge extends AbstractBridge
{
    /**
     * {@inheritdoc}
     */
    public function transform(JcsdlQuery $jcsdlQuery)
    {
        $logic = $jcsdlQuery->getLogic();
        $query = array();
        if (in_array($logic, array('AND', 'OR'))) {
            // Simple logic
            switch ($logic) {
                case 'AND':
                    $operator = '$and';
                    break;
                default:
                    $operator = '$or';
                    break;
            }
            foreach ($jcsdlQuery->getFilters() as $key => $filter) {
                $filter = $this->getFilter($jcsdlQuery, $key);
                $query[$operator][$key] = $filter;
            }
        } else {
            // Advanced logic
            $query = $this->recursive($jcsdlQuery, $logic);
        }

        return $query;
    }

    private function getFilter(JcsdlQuery $jcsdlQuery, $index, $not = false)
    {
        $filter = $jcsdlQuery->getFilters()->get($index);
        switch (trim($filter->getOperator())) {
            case 'in':
            case 'contains_any':
                $values = array();
                foreach (explode(',', $filter->getValue()) as $value) {
                    $value = preg_quote($value);
                    $value = str_replace('\*', '.*', $value);
                    $value = new \MongoRegex(sprintf('/%s/i', $value));
                    array_push($values, $value);
                }
                $mongoQuery = array($not ? '$nin' : '$in' => $values);
                break;
            case 'exists':
                if ($not) {
                    $mongoQuery = array('$exists' => false);
                } else {
                    $mongoQuery = array('$exists' => true);
                }
                break;
            case '==':
                if ($not) {
                    $mongoQuery = array('$ne' => $filter->getValue());
                } else {
                    $mongoQuery = $filter->getValue();
                }
                break;
            case '!=':
                if ($not) {
                    $mongoQuery = $filter->getValue();
                } else {
                    $mongoQuery = array('$ne' => $filter->getValue());
                }
                break;
            case 'regex_partial':
            case 'regex_exact':
                $mongoQuery = new \MongoRegex(sprintf('/%s/i', $filter->getValue()));
                if ($not) {
                    $mongoQuery = array('$not' => $mongoQuery);
                }
                break;
            case '<=':
                if ($not) {
                    $mongoQuery = array('$gt' => $filter->getValue());
                } else {
                    $mongoQuery = array('$lte' => $filter->getValue());
                }
                break;
            case '>=':
                if ($not) {
                    $mongoQuery = array('$lt' => $filter->getValue());
                } else {
                    $mongoQuery = array('$gte' => $filter->getValue());
                }
                break;
            case '<':
                if ($not) {
                    $mongoQuery = array('$gte' => $filter->getValue());
                } else {
                    $mongoQuery = array('$lt' => $filter->getValue());
                }
                break;
            case '>':
                if ($not) {
                    $mongoQuery = array('$lte' => $filter->getValue());
                } else {
                    $mongoQuery = array('$gt' => $filter->getValue());
                }
                break;
            case 'wildcard':
                $value = preg_quote($filter->getValue());
                $value = str_replace('\*', '.*', $value);
                
                $mongoQuery = new \MongoRegex(sprintf('/%s/i', $value));
                if ($not) {
                    $mongoQuery = array('$not' => $mongoQuery);
                }
                break;
            case 'url_in':
            case 'contains_near':
            case 'contains_all':
            case 'substr':
            case 'geo_box':
            case 'geo_radius':
            case 'geo_polygon':
                throw new \Exception('Operator not yet supported');
            default:
                throw new \Exception('Unknown operator given: ' . $filter->getOperator());
                break;
        }

        return array($filter->getField() => $mongoQuery);
    }

    private function recursive(JcsdlQuery $jcsdlQuery, $logic)
    {
        $logic = str_replace(' ', '', $logic);
        $array = array();
        if (substr($logic, -1) !== ')') {
            $logic = "($logic)";
        }
        preg_match('/\((.*)([\|\&])(.*)\)/i', $logic, $match);
        if (count($match) !== 4) {
            return $array;
        }

        $logic = $match[1].$match[2].$match[3];
        switch ($match[2]) {
            case '|':
                $operator = '$or';
                break;
            case '&':
                $operator = '$and';
                break;
            default:
                throw JcsdlException('Unknown operator given: ' . $match[2]);
                break;
        }
        
        $array[$operator][] = $this->processMatching($jcsdlQuery, $match[1]);
        $array[$operator][] = $this->processMatching($jcsdlQuery, $match[3]);

        return $array;
    }

    private function processMatching(JcsdlQuery $jcsdlQuery, $match, $not = false)
    {
        if (strpos($match, '!') === 0) {
            $match = substr($match, 1);
            $not = true;
        }

        if (is_numeric($match)) {
            $data = $this->getFilter($jcsdlQuery, intval($match) - 1, $not);
        } elseif (is_numeric($match) === false) {
            $data = $this->recursive($jcsdlQuery, $match);
        }

        return $data;
    }
}