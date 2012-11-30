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
  PREFIX dbprop: <http://dbpedia.org/property/>
  PREFIX dbcat: <http://dbpedia.org/resource/Category:>
  PREFIX skos: <http://www.w3.org/2004/02/skos/core#> 
  SELECT DISTINCT ?poet_name ?surname ?poet_wiki_link ?abstract 
  WHERE 
  {  ?poet dcterms:subject dbcat:American_poets ;
	  foaf:name ?poet_name ;
	  foaf:surname ?surname ;
	  dbpedia-owl:abstract ?abstract ;
	  foaf:isPrimaryTopicOf ?poet_wiki_link .

        FILTER regex (?poet_name, "Jones")
	FILTER ( lang(?abstract) = "en" )
  } LIMIT 4000
EOQ;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Information about American Poets</title>
<link rel="stylesheet" href="../_css/colorbox.css" />
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>-->


<style type="text/css" title="currentStyle">			
			@import "../media/css/demo_table_jui.css";
			@import "../examples_support/themes/smoothness/jquery-ui-1.8.4.custom.css";
		</style>
		<script type="text/javascript" language="javascript" src="../media/js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				oTable = $('#example').dataTable({
					"bJQueryUI": true,
					"bSort": false,
					"sPaginationType": "full_numbers"
				});
				$('a').addClass('iframe');
			} );
		</script>
<script src="../_js/colorbox/jquery.colorbox-min.js"></script>
</head>
<body> <!--id="example"-->
<table border="2" class="display" id="example">
<thead>
<th colspan="3">American Poets Containing "Jones" In Name</th>
<tr><td>Name of Poet</td><td>Abstract</td></tr>
</thead>
<tbody>
<?php

$result=sparqlQuery($query, 'http://dbpedia.org/sparql');
$results=$result->results->bindings;
if (count($results) > 0) {
  foreach($results as $res) {
   	echo "<tr><td>";
	echo "<a class='iframe' href='";
	echo $res->poet_wiki_link->value;
	echo "'>";	
		echo $res->poet_name->value;
	echo "</a>";	 
	 echo "</td>";
	 echo "<td>";
	 echo $res->abstract->value;
	  echo "</td>";
     echo "</tr>";
  }
}


	
?>
</tbody>
</table>
<script>
    $("td a.iframe").colorbox({iframe:true, width:"90%", height:"90%"});

</script>
</body>
</html>