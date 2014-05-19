<?php

namespace Eo\Jcsdl\Tests;

use Eo\Jcsdl\Parser;
use Eo\Jcsdl\Bridge\MongoBridge;

class MongoBridgeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Parse test
     */
    public function testParse()
    {
        $query = '// JCSDL_MASTER afea624c76586c8e38a60d34880998f6 (!1|3)&2
// JCSDL_VERSION 2.0
(
// JCSDL_START 2dba02c4ee025da6003576c3fd6b5ac0 bitly.geo_city,contains_any,29-7 1
bitly.geo_city contains_any "Chicago"
// JCSDL_END
OR
// JCSDL_START d9e1274c1c6785f3907b8f7af7852dd6 bitly.timezone,equals,19-19 3
bitly.timezone == "America/Los_Angeles"
// JCSDL_END
) AND
// JCSDL_START 32d699b8f6f3c7e68ad382caa264a852 bitly.domain,equals,17-8 2
bitly.domain == "espn.com"
// JCSDL_END
// JCSDL_MASTER_END';

        // Parse JCSDL query
        $parser = new Parser();
        $jcsdlQuery = $parser->parse($query);
        $filters = $jcsdlQuery->getFilters();

        // Transform JCSDL in Mongo query
        $bridge = new MongoBridge();
        $mongoQuery = $bridge->transform($jcsdlQuery);

        $this->assertEquals(json_encode($mongoQuery), '{"$and":[{"$or":[{"bitly.geo_city":{"$nin":[{"regex":"Chicago","flags":"i"}]}},{"bitly.domain":"espn.com"}]},{"bitly.timezone":"America\/Los_Angeles"}]}');
    }
}