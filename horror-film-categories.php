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
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
SELECT DISTINCT ?Film_category_name ?film_name ?film_wiki_link 
WHERE {
  ?category skos:broader category:Horror_films ;
              skos:prefLabel ?Film_category_name .
    ?film dcterms:subject ?category ;
          dbprop:name ?film_name ;
          foaf:isPrimaryTopicOf ?film_wiki_link .

} 
EOQ;
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Films About Halloween</title>
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
					"sPaginationType": "full_numbers"
				});
			} );
		</script>
<script src="../_js/colorbox/jquery.colorbox-min.js"></script>
</head>
<body> <!--id="example"-->
<table border="2" class="display" id="example">
<thead>
<!--<th colspan="3">Films Based On Halloween</th>-->
<tr><td>Name of Film</td><td>Film Category</td></tr>
</thead>
<tbody>
<?php
$result=sparqlQuery($query, 'http://dbpedia.org/sparql');
$results=$result->results->bindings;
//print_r($results);
$filmArr = array();
$catArr = array();
/*$starsArr = array();*/
$i = 0;

foreach($results as $res) {
    echo "<tr><td>";
	echo "<a class=\"iframe\" href=\"";
    echo $res->film_wiki_link->value;
	echo "\">";
  	echo $res->film_name->value;
	echo "</a></td>";
	echo "<td>";
	echo $res->Film_cagegory_name->value;
	echo "</td>";
	echo "</tr>";
 
}
/*$results=$result->results->bindings;
 foreach($results as $res) {
    echo "<tr><td>";
	echo "<a href=\"";
    print $res->link->value;
		echo "\">";
  	print $res->Film_name->value;
	 echo "</a>";
	 echo "</td>";
	 echo "<td>";
	 print $res->name_of_director->value;
     echo "</td>";
     echo "<td>";
	 print $res->name_of_star->value;
	 echo	 "</tr>";
  } */
?>
</tbody>
</table>
<script>
    $("td a.iframe").colorbox({iframe:true, width:"90%", height:"90%"});

</script>
</body>
</html>