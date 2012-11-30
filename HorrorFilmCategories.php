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
<meta charset="utf-8">
<title>Horror Film Categories</title>
<link rel="stylesheet" href="../_css/colorbox.css" />


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
<h2>Horror Film Categories</h2>
<p>This listing presents Horror Films by Category.  The Category list comes from DBpedia, which gets it's data from Wikipedia.  It is displayed in a table.  Each column moving from left to right below is a nesting of more and more narrow categories.</p>
<h2>Narrower Equals More Specific</h2>
<p>You could also think of Narrower Categories as being more specific.</p>
<p> So for example, the top category is Horror Films.  Horror Films is a broader Category than Horror Films By Genre, as one example.  Below that, in a heirarchy of Categories, we have Psychological Horror Films of which Films by Genre is a broader category than Psychological Horror Films.  Note, these are just examples, as there are other categories shown below.</p>
<p>The use of the heading 'Narrower' below represents this heirarchy of contained Categories</p>
<p>By clicking on the links below you can view pages that offer a list of films in each Category.</p>
<p>Enjoy!!!</p>
<h3>First Set of Categories</h3>
<table cellpadding="5" cellspacing="5" width="90%" id="example">
<thead bgcolor="#FF9900">
  <tr>
    <td><h3>Broad Categories:</h3></td>
    <td><h3>Narrower Categories:</h3></td>
    <td><h3>Narrower Categories</h3></td>
  </tr>
 </thead>
<?php
$result=sparqlQuery($query, 'http://dbpedia.org/sparql');
$results=$result->results->bindings;
//print_r($results);
$cat1Arr = array();
$cat2Arr = array();
$cat3Arr = array();
$i = 0;

foreach($results as $res) {
 if(!in_array($res->cat1_name->value,$cat1Arr)){
	if($j!=0)
	$i++;
	$cat1Arr[$i] = $res->cat1_name->value;
	
	$cat2Arr[$i] = $res->cat2_name->value;
	
	$j=0;
	if(@!in_array($res->cat3_name->value,$cat3Arr[$i]))
	$cat3Arr[$i][$j] = $res->cat3_name->value;
	
	
	//echo $cat1Arr[$i]." -- ".$cat2Arr[$i]." => ".$cat3Arr[$i][$j]."</br>";
	$j++;
 }
 else {	
 	 if(@!in_array($res->cat3_name->value,$cat3Arr[$i]))
	 $cat3Arr[$i][$j] = $res->cat3_name->value;
	 
	 
	// echo $cat1Arr[$i]." -- ".$cat2Arr[$i]." => ".$cat3Arr[$i][$j]."</br>";
	 $j++;
 }
}

 $cnt = count($cat1Arr);
for($m=0;$m<$cnt;$m++){
	
	$cat1_name = $cat1Arr[$m];
	
	$cat2_name = $cat2Arr[$m];
	
	
	$cat3Cnt = count($cat3Arr[$m]);
	$cat3_names = "";
	
	
	for($l=0;$l<$cat3Cnt;$l++){
		$cat3list = $cat3Arr[$m][$l];
		$cat3_name = $cat3list[0];
		$cat3link = str_replace("http%3A%2F%2F","",$cat3list[1]);
		
		$cat3_names.='<a class="iframe" href="'.$cat3link.'">'.$cat3_name.'</a>'.', ';
		//$stars.= $cat3Arr[$m][$l].", ";
	}
	
	
	echo "<tr><td>";
	echo $cat1_name;
	  echo "</td>";
	 echo "<td>";
	 echo $cat2_name;
	  echo "</td>";
     echo "<td>".$cat3_names."</tr>";
	
	 
}

?>
</tbody>
</table>
<script>
    $("td a.iframe").colorbox({iframe:true, width:"90%", height:"90%"});

</script>
</body>
</html>