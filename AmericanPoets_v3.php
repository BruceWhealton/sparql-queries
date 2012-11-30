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
SELECT DISTINCT ?poet_name ?surname ?poet_wiki_link ?abstract ?depiction ?artist1_name 
?artist1_wiki_link 
WHERE {  ?poet dcterms:subject <http://dbpedia.org/resource/Category:American_poets> ;
 	        foaf:name ?poet_name ;
                foaf:surname ?surname ;
                foaf:depiction ?depiction ;
		dbpedia-owl:abstract ?abstract ;
            foaf:isPrimaryTopicOf ?poet_wiki_link .
  OPTIONAL {
      ?artist1 dbpedia-owl:influencedBy ?poet ;
               foaf:name ?artist1_name ;
               foaf:isPrimaryTopicOf ?artist1_wiki_link .
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
<th colspan="4">American Poets and their Influences</th>
<tr><td colspan="4"><p style="font-size:1.2em">The first column lists a poet, followed by an abstract about the poet, and then information about their influences - who they influenced or who was influential to them.</p>
<h1 style="color: #DC2E06; text-align: center;">Please wait...  This may take a while to load<br />
Loading over 1100 Poets</h1>
</td></tr>
<tr><td width="20%">Name of Poet</td><td width="40%">Poet Abstract</td><td width="20%">Influenced by:</td><td width="20%">Influenced</td></tr>
</thead>
<tbody>
<?php
$result=sparqlQuery($query, 'http://dbpedia.org/sparql');
$results=$result->results->bindings;
console.log($results[]);
//print_r($results);
$poetArr = array();
$artist1Arr = array();
$artist2Arr = array();
$i = 0;

foreach($results as $res) {
 if(!in_array($res->poet_name->value."-".$res->poet_wiki_link->value."-".$res->abstract->value,$poetArr)){
	if($j!=0)
	$i++;
	$poetArr[$i] = $res->poet_name->value."-".$res->poet_wiki_link->value."-".$res->abstract->value;
	
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
	$pabstract = $poet[2];
	
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
	 echo "<td>";
	 echo $pabstract; 
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