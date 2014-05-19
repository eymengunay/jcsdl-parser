<?php

$loader = require_once __DIR__ . "/../vendor/autoload.php";

use Eo\Jcsdl\Parser;
use Eo\Jcsdl\Bridge\ElasticsearchBridge;

// Sample JCSDL query
$query = '// JCSDL_MASTER afea624c76586c8e38a60d34880998f6 1&2&3
// JCSDL_VERSION 2.1
// JCSDL_START 2dba02c4ee025da6003576c3fd6b5ac0 bitly.geo_city,contains_any,29-7 1
bitly.geo_city contains_any "Chicago"
// JCSDL_END
AND
// JCSDL_START d9e1274c1c6785f3907b8f7af7852dd6 bitly.timezone,equals,19-19 3
bitly.timezone == "America/Los_Angeles"
// JCSDL_END
AND
// JCSDL_START 32d699b8f6f3c7e68ad382caa264a852 bitly.domain,equals,17-8 2
email == "eymen@egunay.com"
// JCSDL_END
// JCSDL_MASTER_END';

$query = '// JCSDL_MASTER 922d477dd482a0057f2dffc5a3a1ad8a AND
// JCSDL_VERSION 2.0
// JCSDL_START bcf98963ba9331a0ca415fff6bdbdff0 interaction.content,contains_any,34-7 1
interaction.content contains_any "Foo,Bar"
// JCSDL_END
AND
// JCSDL_START 4c077d198c8d4de87668976f18e68033 interaction.content,exists 2
interaction.content exists 
// JCSDL_END
AND
// JCSDL_START 5d60fe2224459aa0338d8571f0a2e7d1 interaction.content,substr,28-3 3
interaction.content substr "Foo"
// JCSDL_END
AND
// JCSDL_START c3c8dc39f292b1deefe3238ab5115b39 interaction.content,contains_near,35-9 4
interaction.content contains_near "Foo,Bar:2"
// JCSDL_END
AND
// JCSDL_START c88cb4bebe1e55700eed0c7d4b1b9248 interaction.content,different,24-3 5
interaction.content != "Foo"
// JCSDL_END
AND
// JCSDL_START 2bc757e529148788382cad12e28efb2b interaction.content,regex_partial,35-3 6
interaction.content regex_partial "foo"
// JCSDL_END
AND
// JCSDL_START f544cf9e954aaea12357e820b5ecf3bd interaction.content,regex_exact,33-7 7
interaction.content regex_exact ".*bar.*"
// JCSDL_END
// JCSDL_MASTER_END';

$query = '// JCSDL_MASTER 693ebc49f6ab7c72b8dad1a13cddf550 AND
// JCSDL_VERSION 2.1
// JCSDL_START 3b5a546dce15fc46fe2577b5479b0763 reddit.contenttype,exists 1
reddit.contenttype exists 
// JCSDL_END
AND
// JCSDL_START e34d3cda7912b9d70df6caa2f8e3c061 reddit.author.name,equals,23-5 2
reddit.author.name == "Eymen"
// JCSDL_END
// JCSDL_MASTER_END';

// Parse JCSDL query
$parser = new Parser();
$jcsdlQuery = $parser->parse($query);

// Transform JCSDL in Elasticsearch query
$bridge = new ElasticsearchBridge();
$elasticsearchQuery = $bridge->transform($jcsdlQuery);

echo "JCSDL query:\n";
echo "============\n";
echo $query;

echo "\n\n\n";

echo "Elastic query:\n";
echo "==============\n";
print_r($elasticsearchQuery);