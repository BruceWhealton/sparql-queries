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
SELECT DISTINCT ?poet ?poet_name ?surname ?poet_wiki_link ?abstract ?depiction ?artist_name 
?artist_wiki_link 
WHERE 
{  ?poet dcterms:subject <http://dbpedia.org/resource/Category:American_poets> ;
 	        foaf:name ?poet_name ;
                foaf:surname ?surname ;
                foaf:depiction ?depiction ;
		dbpedia-owl:abstract ?abstract ;
            foaf:isPrimaryTopicOf ?poet_wiki_link .
  OPTIONAL {
      ?artist dbpedia-owl:influencedBy ?poet ;
               foaf:name ?artist_name ;
               foaf:isPrimaryTopicOf ?artist_wiki_link .
   }
  FILTER ( lang(?abstract) = "en" )
} ORDER BY ASC(?surname) 
LIMIT 4000
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
tr th {
	font-family: Tahoma, Geneva, sans-serif;
	font-size: 1.8em;
	font-weight: bold;
	font-variant: small-caps;
	color: rgba(0,102,204,1);
}
tr td ul li {
	list-style-position: inside;
	list-style-type: circle;
}
</style>
		<script type="text/javascript" language="javascript" src="../media/js/jquery.js"></script>
		<script type="text/javascript" language="javascript" src="../media/js/jquery.dataTables.js"></script>
		<script type="text/javascript" charset="utf-8">
			$(document).ready(function() {
				oTable = $('#example').dataTable({
					"bJQueryUI": true,
					"bSort": false,
					    "aoColumns": [
						  null,
						  null,
						  { "bSearchable": false },
						  null
					], 
					"sPaginationType": "full_numbers"
				});
			} );
		</script>
<script src="../_js/colorbox/jquery.colorbox-min.js"></script>
</head>
<body> <!--id="example"-->
<table border="2" class="display" id="example">
<thead>
<th colspan="4">American Poets and their Influences</th>
<tr><td colspan="4"><p style="font-size:1.2em">The first column lists a poet, followed by an abstract about the poet, and then information about their influences - who they influenced or who was influential to them.</p>
<h1 style="color: #DC2E06; text-align: center;">Please wait...  This may take a while to load<br />
Loading over 1100 Poets</h1>
</td></tr>
<tr><td width="20%">Name of Poet</td><td width="40%">Poet Abstract</td><td width="20%">Photo:</td><td width="20%">Influenced:</td></tr>
</thead>
<tbody>
<?php
$result=sparqlQuery($query, 'http://dbpedia.org/sparql');
$results=$result->results->bindings;
//print_r($results);
$poetArray = array();
$artist1Array = array();

$i = 0;
$j = 0;

foreach($results as $res) {

 if($res->poet_name->value != $poetArray[$i][name]) {
	 $poetArray[$i][name] = $res->poet_name->value;
	 $poetArray[$i][wiki_url] = $res->poet_wiki_link->value;


 }
    $poetArray[$i][poet_abstract] = $res->abstract->value;
	$poetArray[$i][artist_name_list][$j][artist_name] = $res->artist_name->value;
	$poetArray[$i][artist_name_list][$j][artist_wiki_link] = $res->artist_wiki_link->value;
	$poetArray[$i][depiction] = $res->depiction->value;
	$i++;
	$j++;	 
}

 $cnt = count($poetArray);
for($m=0;$m<$cnt;$m++){
	echo "<tr><td>";
	echo "<a class='iframe' href='";	
	echo $poetArray[$m][wiki_url]."'>";
	echo $poetArray[$m][name];
	echo "</a>";
    echo "</td>";
	echo "<td>";
    echo $poetArray[$m][poet_abstract]; 
	echo "</td>";
	echo "<td>";
	echo "<img src='$poetArray[$m][depiction]' />";
	echo "</td>";
	echo "<td>";
	echo "<ul><li>";
	echo "<a class='iframe' href='";
	echo $poetArray[$m][artist_name_list][0][artist_wiki_link]."'"." title='$poetArray[$m][artist_name_list][0][artist_name]'";
	echo ">";
	echo $poetArray[$m][artist_name_list][0][artist_name];
	echo "</a>";
	echo "</li>";
	echo "</ul>";
	echo  "</td>";
	echo "</tr>";

	
}
?>
</tbody>
</table>
<script>
    $("td a.iframe").colorbox({iframe:true, width:"90%", height:"90%"});

</script>
</body>
</html>