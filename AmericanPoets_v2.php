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
SELECT DISTINCT ?poet_name  ?poet_wiki_link ?artist1_name ?artist1_wiki_link ?artist2_name ?artist2_wiki_link
WHERE {  ?poet dcterms:subject <http://dbpedia.org/resource/Category:American_poets> ;
 	        foaf:name ?poet_name ;
                foaf:isPrimaryTopicOf ?poet_wiki_link .
  OPTIONAL {
        ?artist1 dbpedia-owl:influenced ?poet ;
                 foaf:name ?artist1_name ;
                 foaf:isPrimaryTopicOf ?artist1_wiki_link .
	}
  OPTIONAL {
      ?artist2 dbpedia-owl:influencedBy ?poet ;
               foaf:name ?artist2_name ;
               foaf:isPrimaryTopicOf ?artist2_wiki_link .
   }
  FILTER ( lang(?poet_name) = "en" )
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
<tr><td>Name of Poet</td><td>Influenced by:</td><td>Influenced</td></tr>
</thead>
<tbody>
<?php
$result=sparqlQuery($query, 'http://dbpedia.org/sparql');
$results=$result->results->bindings;
//print_r($results);
$poetArr = array();
$artist1Arr = array();
$artist2Arr = array();
$i = 0;

foreach($results as $res) {
 if(!in_array($res->poet_name->value."-".$res->poet_wiki_link->value,$poetArr)){
	if($j!=0)
	$i++;
	$poetArr[$i] = $res->poet_name->value."-".$res->poet_wiki_link->value;
	
	$artist1Arr[$i] = $res->artist1_name->value."-".$res->artist1_wiki_link->value;
	
	$j=0;
	if(@!in_array($res->artist2_name->value."-".$res->artist2_wiki_link->value,$artist2Arr[$i]))
	$artist2Arr[$i][$j] = $res->artist2_name->value."-".$res->artist2_wiki_link->value;
	
	
	//echo $poetArr[$i]." -- ".$artist1Arr[$i]." => ".$artist2Arr[$i][$j]."</br>";
	$j++;
 }
 else {	
 	 if(@!in_array($res->artist2_name->value."-".$res->artist2_wiki_link->value,$artist2Arr[$i]))
	 $artist2Arr[$i][$j] = $res->artist2_name->value."-".$res->artist2_wiki_link->value;
	 
	 
	// echo $poetArr[$i]." -- ".$artist1Arr[$i]." => ".$artist2Arr[$i][$j]."</br>";
	 $j++;
 }
}

 $cnt = count($poetArr);
for($m=0;$m<$cnt;$m++){
	
	$poet = explode("-",$poetArr[$m]);
	$pname = $poet[0];
	$plink = $poet[1];
	
	$artist1 = explode("-",$artist1Arr[$m]);
	$a1name = $artist1[0];
	$a1link = $artist1[1];
	
	$artist2Cnt = count($artist2Arr[$m]);
	$artist2_names = "";
	
	
	for($l=0;$l<$starCnt;$l++){
		$artist2list = explode("-",$artist2Arr[$m][$l]);
		$artist2name = $artist2list[0];
		$artist2link = str_replace("http%3A%2F%2F","",$artist2list[1]);
		
		$artist2_names.='<a class="iframe" href="'.$artist2link.'">'.$artist2name.'</a>'.', ';
		//$stars.= $artist2Arr[$m][$l].", ";
	}
	
	
	echo "<tr><td>";
	echo "<a class='iframe' href='";	
	echo $plink;
	 echo "'</a>";
	echo $pname;	 
	  echo "</td>";
	 echo "<td><a class='iframe' href='".$a1link."'>";
	 echo $a1name;
	  echo "</a></td>";
     echo "<td>".$artist2_names."</tr>";
	
	 
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