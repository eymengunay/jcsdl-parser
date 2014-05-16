<?php

/*
 * This file is part of the Jcsdl package.
 *
 * (c) 2014 Eymen Gunay <eymen@egunay.com>
 */

namespace Eo\Jcsdl;

use Eo\Jcsdl\Exception\JcsdlException;

/**
 * Parser
 */
class Parser
{
    /**
     * Parse JCSDL query string
     * 
     * @param  string $query
     * @param  float  $version
     * @return Query
     */
    public function parse($query)
    {
        // Validate query
        $regex = '^\/\/\sJCSDL_MASTER\s([a-f0-9]{32}).*\/\/\sJCSDL_VERSION\s2\.[0-9].*\/\/\sJCSDL_MASTER_END$';
        if (!preg_match(sprintf('/%s/s', $regex), $query, $match)) {
            throw new JcsdlException('Invalid JCSDL query given');
        }

        $logic   = $this->extractQueryLogic($query);
        $filters = $this->extractQueryFilters($query);

        $jcsdlQuery = new Query();
        $jcsdlQuery
            ->setRaw($query)
            ->setId($match[1])
            ->setLogic($logic)
        ;

        foreach ($filters as $filter) {
            $jcsdlQuery->addFilter($filter);
        }

        return $jcsdlQuery;
    }

    /**
     * Extracts logic from JCSDL query string
     *
     * @throws JcsdlException If JCSDL query string is not valid
     * @param  string         $query
     * @return string
     */
    private function extractQueryLogic($query)
    {
        $regex = '^\/\/\sJCSDL_MASTER\s[a-f0-9]{32}\s(.*)\n';
        if (!preg_match(sprintf('/%s/', $regex), $query, $match)) {
            throw new JcsdlException('Could not extract JCSDL query logic');
        }

        $logic = end($match);

        if ($logic === 'AND' or $logic === 'OR') {
            $tmp   = '';
            $count = substr_count($query, '// JCSDL_START');
            for ($i = 1; $i <= $count; $i++) {
                $tmp .= "$i";
                if ($i !== $count) {
                    $tmp .= $logic === 'AND' ? '&' : '|';
                }
            }
            $logic = $tmp;
        }

        return $logic;
    }

    /**
     * Extracts JCSDL query filters
     * 
     * @param  string $query
     * @return array
     */
    private function extractQueryFilters($query)
    {
        $regex = '\/\/\sJCSDL_START\s([a-f0-9]{32}).*\n(.*)\s(.*)\s(?:(.*))?\n\/\/\sJCSDL_END';
        if (!preg_match_all(sprintf('/%s/sU', $regex), $query, $matches)) {
            return array();
        }
        array_shift($matches);
        list($ids, $fields, $operators, $values) = $matches;

        $filters = array();
        for ($i = 0; $i < count($fields); $i++) {
            $filter = new Filter();
            if (preg_match('/^(["\']).*\1$/m', $values[$i])) {
                $value = trim($values[$i], '"');
            } else {
                switch ($values[$i]) {
                    case 'true':
                        $value = true;
                        break;
                    case 'false':
                        $value = false;
                        break;
                    default:
                        $value = intval($values[$i]);
                        break;
                }
            }
            $filter
                ->setId($ids[$i])
                ->setField($fields[$i])
                ->setOperator($operators[$i])
                ->setValue($value)
            ;
            $filters[] = $filter;
        }

        return $filters;
    }
}