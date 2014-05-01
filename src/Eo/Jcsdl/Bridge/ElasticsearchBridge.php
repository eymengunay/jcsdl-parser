<?php

/*
 * This file is part of the Jcsdl package.
 *
 * (c) 2014 Eymen Gunay <eymen@egunay.com>
 */

namespace Eo\Jcsdl\Bridge;

use stdClass;
use Elastica;
use Elastica\Query as ElasticaQuery;
use Elastica\Query\QueryString;
use Eo\Jcsdl\Query as JcsdlQuery;
use Eo\Jcsdl\Filter;
use Eo\Jcsdl\Exception\JcsdlException;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * ElasticsearchBridge
 */
class ElasticsearchBridge extends AbstractBridge
{
    /**
     * {@inheritdoc}
     */
    public function transform(JcsdlQuery $jcsdlQuery)
    {
        $filters = $jcsdlQuery->getFilters();
        $logic = strtr($jcsdlQuery->getLogic(), array(
            '&' => ' AND ',
            '|' => ' OR '
        ));

        $strtr = array();
        $index = $filters->count();
        while ($index) {
            $filter = $filters->offsetGet($index - 1);
            $strtr[$index] = $this->transformElastica($filter);
            --$index;
        }

        $queryString = new QueryString(strtr($logic, $strtr));
        
        return new ElasticaQuery($queryString);
    }

    /**
     * Transforms JCSDL filter to Elastica query
     * 
     * @param  Filter $filter
     * @return string
     */
    private function transformElastica(Filter $filter)
    {
        switch ($filter->getOperator()) {
            case 'contains_any':
                $value = $filter->getValue();
                if (is_scalar($value)) {
                    $value = array($value);
                }
                $str = sprintf('%s:(%s)', $filter->getField(), implode(' ', $value));
                break;
            case 'exists':
                $str = sprintf('_exists_:%s', $filter->getField());
            case '==':
                $str = sprintf('%s:"%s"', $filter->getField(), $filter->getValue());
                break;
            case 'regex_partial':
            case 'regex_exact':
                $str = sprintf('%s:/%s/', $filter->getField(), $filter->getValue());
                break;
            case '<=':
            case '>=':
            case '<':
            case '>':
                $str = sprintf('%s:%s%s', $filter->getField(), $filter->getOperator(), $filter->getValue());
            case 'contains_near':
            case 'substr':
            case '!=':
            case 'geo_box':
            case 'geo_radius':
            case 'geo_polygon':
                throw new JcsdlException('Operator not yet supported');
            default:
                throw new JcsdlException('Unknown operator given: ' . $filter->getOperator());
                break;
        }

        return $str;
    }
}