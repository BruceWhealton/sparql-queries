	var query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
	PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
	PREFIX dcterms: <http://purl.org/dc/terms/>\
	PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>\
	PREFIX dbprop: <http://dbpedia.org/property/>\
	PREFIX dbcat: <http://dbpedia.org/resource/Category:>\
	PREFIX skos: <http://www.w3.org/2004/02/skos/core#> \
	SELECT DISTINCT ?poet ?poet_name ?surname ?poet_wiki_link ?abstract \
	WHERE \
	{  ?poet dcterms:subject dbcat:American_poets;\
		foaf:name ?poet_name ;\
		foaf:surname ?surname ;\
		dbpedia-owl:abstract ?abstract ;\
		foaf:isPrimaryTopicOf ?poet_wiki_link .\
	  FILTER ( lang(?abstract) = \"en\" )\
	} ORDER BY ?surname \
	  LIMIT 20 OFFSET %offset%";
  
  var parseResults = function(data){
	  var i = 0, 
		  len = data.results.bindings.length,
		  domEntry, poet,
		  $result = $("#result");            
	  
	  // empty old stuff
	  $result.empty();
	  // fill in
	  for(i = 0; i < len; i++){
		  poet = data.results.bindings[i];
          poet_name = poet.poet_name.value;
		  domEntry = '<li><a href="#poet-details" ';
		  domEntry += " onclick='javascript:getPoetDetails(\"" + poet_name + "\");' >";
		  domEntry += poet_name;
		  domEntry += '</a></li>';
		  $result.append( $(domEntry) );
	  }
	  
		// refresh style
	  $result.listview('refresh');
	  
	  // hide loader
	  $.mobile.hidePageLoadingMsg();
  };
  
  var getPoets = function(skip){
	  // show loader
	  $.mobile.showPageLoadingMsg();
	  
	  if( skip == null || typeof skip == "undefined" ) skip = 0;
	  
	  var prepQuery = query.replace("%offset%", skip);
	  var url = "http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=" + escape(prepQuery) + "&format=json";
  
	$.getJSON(url, parseResults);
  }
  
  //
  $(document).ready(function(){
	  var skip = 0;
	  
	  $("#prev").click(function(){
		  skip -= 20;
		  if( skip < 0 ) skip = 0;
		  getPoets(skip);
	  });
  
	  $("#next").click(function(){
		  skip += 20;
		  getPoets(skip);
	  });
	  
	   getPoets(skip);
  });
 	  	var poetquery = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
	PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
	PREFIX dcterms: <http://purl.org/dc/terms/>\
	PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>\
	PREFIX dbprop: <http://dbpedia.org/property/>\
	PREFIX dbcat: <http://dbpedia.org/resource/Category:>\
	PREFIX skos: <http://www.w3.org/2004/02/skos/core#> \
	SELECT DISTINCT ?poet_name ?surname ?poet_wiki_link ?abstract  \
	?creative_work_name \
	WHERE \
	{  ?poet foaf:name  \"%name%\"@en ;\
		foaf:name ?poet_name ;\
		dbpedia-owl:abstract ?abstract ;\
		foaf:isPrimaryTopicOf ?poet_wiki_link .\
		OPTIONAL { \
		?creative_work dbpedia-owl:writer ?poet ; \
		               dbprop:title ?creative_work_name . \
		} \
	  FILTER ( lang(?abstract) = \"en\" )\
	}";
 
 var parsePoetResults = function(data){
     
	  var i = 0, 
		  len = data.results.bindings.length,
		  domEntry, poet_details,
		  $result_details = $("#result_details");            
	  
		  // empty old stuff
		  $result_details.empty();
		  // fill in
	
		  poet_details = data.results.bindings[i];	
		  poet_name = poet_details.poet_name.value;	  
		  poet_surname = poet_details.surname.value;
		  alert(poet_surname);
		  domEntry = '<h1>' + poet_surname + '</h1>';
		  domEntry += '<p>' + poet_details.abstract.value + '</p>';
		  domEntry += '<ul>';
		  //alert(poet_details.creative_work_name.value);
		  /*for(j=0; j < length(poet_details.creative_work_name); j++)
		  {
		  domEntry += '<li>' + poet_details.creative_work_name[j].value;
		  domEntry += '</li>';
		  }*/
		  domEntry += '</ul>';
		  $result_details.append( $(domEntry) );

		// refresh style
	  $result_details.listview('refresh');
	  
	  // hide loader
	  $.mobile.hidePageLoadingMsg();
  };
  
  var getPoetDetails = function(poet_name){
	  // show loader	  
	  $.mobile.showPageLoadingMsg();
	  var prepQuery = poetquery.replace("%name%", poet_name);
	  var url = "http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=" + escape(prepQuery) + "&format=json";
    
	$.getJSON(url, parsePoetResults);
  }