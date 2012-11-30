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
SELECT DISTINCT ?Poet_name  ?poet_wiki_link ?name_of_artist1 ?artist1_wiki_link ?name_of_artist2 ?artist2_wiki_link
WHERE {  ?poet1 dcterms:subject <http://dbpedia.org/resource/Category:American_poets> ;
 	        foaf:name ?Poet_name ;
                foaf:isPrimaryTopicOf ?poet_wiki_link .
  OPTIONAL {
        ?poet dbpedia-owl:influencedBy ?artist1 ;
                 foaf:name ?name_of_artist1 ;
                 foaf:isPrimaryTopicOf ?artist1_wiki_link .
	}
  OPTIONAL {
      ?artist2 dbpedia-owl:influencedBy ?poet1 ;
               foaf:name ?name_of_artist2 ;
               foaf:isPrimaryTopicOf ?artist2_wiki_link .
   }
  
} LIMIT 400
EOQ;
?>
<!doctype html>
<html>
		<head>
		<meta charset="utf-8">
		<title>
		American Poets/title>
		<link rel="stylesheet" href="../_css/colorbox.css" />
        
		<style type="text/css" title="currentStyle">
@import url("../media/css/demo_table_jui.css");
@import url("../examples_support/themes/smoothness/jquery-ui-1.8.4.custom.css");
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
		<body>
<!--id="example"-->
<table border="2" class="display" id="example">
          <thead>
    <!--<th colspan="3">Films Based On Halloween</th>-->
    <tr>
              <td>Name of Poet</td>
              <td>Influenced by:</td>
              <td>Influenced</td>
            </tr>
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
 if(!in_array($res->Poet_name->value."-".$res->poet_wiki_link->value,$poetArr)){
	if($j!=0)
	$i++;
	$poetArr[$i] = $res->Poet_name->value."-".$res->poet_wiki_link->value;
	
	$artist1Arr[$i] = $res->name_of_artist1->value."-".$res->artist1_wiki_link->value;
	
	$j=0;
	if(@!in_array($res->name_of_artist2->value."-".$res->artist2_wiki_link->value,$artist2Arr[$i]))
	$artist2Arr[$i][$j] = $res->name_of_artist2->value."-".$res->artist2_wiki_link->value;
	
	
	//echo $poetArr[$i]." -- ".$artist1Arr[$i]." => ".$artist2Arr[$i][$j]."</br>";
	$j++;
 }
 else {	
 	 if(@!in_array($res->name_of_artist2->value."-".$res->artist2_wiki_link->value,$artist2Arr[$i]))
	 $artist2Arr[$i][$j] = $res->name_of_artist2->value."-".$res->artist2_wiki_link->value;
	 
	 
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
	$artist2names = "";
	
	
	for($l=0;$l<$artist2Cnt;$l++){
		$artist2list = explode("-",$artist2Arr[$m][$l]);
		$a2name = $artist2list[0];
		$a2link = str_replace("http%3A%2F%2F","",$artist2list[1]);
		
		$artist2names.='<a class="iframe" href="'.$a2link.'">'.$a2name.'</a>'.', ';
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
     echo "<td>".$artist2names."</tr>";
	
	 
}

?>
  </tbody>
        </table>
<script>
    $("td a.iframe").colorbox({iframe:true, width:"90%", height:"90%"});

</script>
</body>
</html>