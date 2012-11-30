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
PREFIX dbprop: <http://dbpedia.org/property/>
PREFIX db: <http://dbpedia.org/resource/>
SELECT DISTINCT ?cat1_name ?cat2_name ?cat3_name 
WHERE {
  ?cat1 skos:broader category:Horror_films ;
             skos:prefLabel ?cat1_name .
    FILTER ( lang(?cat1_name) = "en" ).
  ?cat2 skos:broader ?cat1 ;
        skos:prefLabel ?cat2_name .
    FILTER ( lang(?cat2_name) = "en" ) .
	     
    OPTIONAL { ?cat3 skos:broader ?cat2 ;
        skos:prefLabel ?cat3_name .
        }
} ORDER BY ASC(?cat1_name)
EOQ;
?>
<!doctype html>
<html>
<head>
<link rel="stylesheet" href="../_css/colorbox.css" />
<link href="../_css/custom.css" rel="stylesheet" type="text/css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="application/javascript" src="../_js/colorbox/jquery.colorbox-min.js"></script>
<script>
	$(document).ready(function(e) {
        $(".iframe").colorbox({
			iframe:true, width:"100%", height:"100%"
		});
    });
</script>
<style type="text/css" title="currentStyle">			
			@import "media/css/demo_table_jui.css";
			@import "examples_support/themes/smoothness/jquery-ui-1.8.4.custom.css";
		</style>
		<script type="text/javascript" language="javascript" src="media/js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="media/js/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				oTable = $('#example').dataTable({
					"bJQueryUI": true,
					"bsort": false,
					"sPaginationType": "full_numbers"
				});
				$('a').addClass('iframe');
			} );
		</script>
<meta charset="utf-8">
<title>Horror Movie Categories</title>
</head>
<body>

  <?php
$result=sparqlQuery($query, 'http://dbpedia.org/sparql');
$results=$result->results->bindings;
//print_r($results);
 foreach($results as $res) {
    echo "<tr><td>";
	echo "<a href=\"";
    echo str_replace(' ', '-',$res->cat1_name->value);
	echo ".php \">";
  	echo $res->cat1_name->value;
	echo "</a>";
	echo "</td>";
	echo "<td>";
	echo "<a href=\"";
    echo str_replace(' ', '-', $res->cat2_name->value);
	echo ".php \">";
  	echo $res->cat2_name->value;
	echo "</a>";
	echo "</td>";
	echo "<td>";
	echo "<a href=\"";
    echo str_replace(' ', '-', $res->cat3_name->value);
	echo ".php \">";
  	echo $res->cat3_name->value;
	echo "</a>";
	echo "</td></tr>";
  } 
?>
</table>

</body>
</html>