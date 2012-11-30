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
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
PREFIX dcterms: <http://purl.org/dc/terms/>
PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>
PREFIX skos: <http://www.w3.org/2004/02/skos/core#>
PREFIX dbprop: <http://dbpedia.org/property/>
SELECT ?Film_name ?link ?name_of_director ?director_wiki_link ?name_of_star ?star_wiki_link
WHERE {
  ?film dcterms:subject category:Singaporean_horror_films ;
              dbprop:name ?Film_name ;
           foaf:isPrimaryTopicOf ?link .

  OPTIONAL {
	 ?film dbpedia-owl:director ?director .
    ?director foaf:isPrimaryTopicOf ?director_wiki_link ;
	           foaf:name ?name_of_director .
}
  OPTIONAL {
	  ?film  dbpedia-owl:starring ?star .
    ?star  foaf:isPrimaryTopicOf ?star_wiki_link ;
	        foaf:name ?name_of_star .
			
  }
          
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
<tr><td>Name of Film</td><td>Directed by:</td><td>Starring</td></tr>
</thead>
<tbody>
<?php
$result=sparqlQuery($query, 'http://dbpedia.org/sparql');
$results=$result->results->bindings;
//print_r($results);
$filmArr = array();
$dirArr = array();
$starsArr = array();
$i = 0;

foreach($results as $res) {
 if(!in_array($res->Film_name->value."-".$res->link->value,$filmArr)){
	if($j!=0)
	$i++;
	$filmArr[$i] = $res->Film_name->value."-".$res->link->value;
	
	$dirArr[$i] = $res->name_of_director->value."-".$res->director_wiki_link->value;
	
	$j=0;
	if(@!in_array($res->name_of_star->value."-".$res->star_wiki_link->value,$starsArr[$i]))
	$starsArr[$i][$j] = $res->name_of_star->value."-".$res->star_wiki_link->value;
	
	
	//echo $filmArr[$i]." -- ".$dirArr[$i]." => ".$starsArr[$i][$j]."</br>";
	$j++;
 }
 else {	
 	 if(@!in_array($res->name_of_star->value."-".$res->star_wiki_link->value,$starsArr[$i]))
	 $starsArr[$i][$j] = $res->name_of_star->value."-".$res->star_wiki_link->value;
	 
	 
	// echo $filmArr[$i]." -- ".$dirArr[$i]." => ".$starsArr[$i][$j]."</br>";
	 $j++;
 }
}

 $cnt = count($filmArr);
for($m=0;$m<$cnt;$m++){
	
	$film = explode("-",$filmArr[$m]);
	$fname = $film[0];
	$flink = $film[1];
	
	$director = explode("-",$dirArr[$m]);
	$dname = $director[0];
	$dlink = $director[1];
	
	$starCnt = count($starsArr[$m]);
	$stars = "";
	
	
	for($l=0;$l<$starCnt;$l++){
		$starlist = explode("-",$starsArr[$m][$l]);
		$starname = $starlist[0];
		$starlink = str_replace("http%3A%2F%2F","",$starlist[1]);
		
		$stars.='<a class="iframe" href="'.$starlink.'">'.$starname.'</a>'.', ';
		//$stars.= $starsArr[$m][$l].", ";
	}
	
	
	echo "<tr><td>";
	echo "<a class='iframe' href='";	
	echo $flink;
	 echo "'</a>";
	echo $fname;	 
	  echo "</td>";
	 echo "<td><a class='iframe' href='".$dlink."'>";
	 echo $dname;
	  echo "</a></td>";
     echo "<td>".$stars."</tr>";
	
	 
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