<?php
function sparqlQuery($query, $baseURL, $format="application/json") {
  $params=array(
                "default-graph" =>  "",
                "should-sponge" =>  "soft",
                "query" =>  $query,
                "debug" =>  "on",
                "timeout" =>  "",
                "format" =>  $format,
                "save" =>  "display",
                "fname" =>  ""
                );
  $querypart="?";
  foreach($params as $name => $value) {
    $querypart=$querypart . $name . '=' . urlencode($value) . "&";
  }
  $sparqlURL=$baseURL . $querypart;
  return json_decode(file_get_contents($sparqlURL));
};

$query=<<<EOQ
PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>
PREFIX skos: <http://www.w3.org/2004/02/skos/core#> 
SELECT DISTINCT ?poet_name ?poet_wiki_link
WHERE {  ?poet dcterms:subject <http://dbpedia.org/resource/Category:American_poets> ;
               foaf:name ?poet_name ;
               foaf:isPrimaryTopicOf ?poet_wiki_link .
 
 } ORDER BY ?poet_name
EOQ;
?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="../_css/colorbox.css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="application/javascript" src="../_js/colorbox/jquery.colorbox-min.js"></script>
<script>
	$(document).ready(function(e) {
        $(".iframe").colorbox({
			iframe:true, width:"100%", height:"100%"
		});
    });
</script>
<meta charset="utf-8">
<title>American Poets - Information</title>
</head>
<body>
<table border="2">
<th colspan="3">American Poets</th>
<tr><td>Name of Poet</td></tr>
<?php
$result=sparqlQuery($query, 'http://dbpedia.org/sparql');
$results=$result->results->bindings;
//print_r($results);
 foreach($results as $res) {
    echo "<tr><td>";
	echo "<a class=\"iframe\" href=\"";
    echo $res->poet_wiki_link->value;
	echo "\">";
  	echo $res->poet_name->value;
	echo "</a></td>";
	echo "</tr>";
  } 
?>
</table>

</body>
</html>