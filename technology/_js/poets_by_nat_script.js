// First get a list of all Poets by Nationalities - all Nationalities will be listed

        var init_query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
    PREFIX dcterms: <http://purl.org/dc/terms/>\
    PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>\
    PREFIX dbprop: <http://dbpedia.org/property/>\
    PREFIX dbcat: <http://dbpedia.org/resource/Category:>\
    PREFIX skos: <http://www.w3.org/2004/02/skos/core#> \
    SELECT DISTINCT ?poet_cat ?poet_cat_name \
    WHERE \
    {  ?poet_cat skos:broader dbcat:Poets_by_nationality ;  \
                 skos:prefLabel ?poet_cat_name .	\
    } ORDER BY ?poet_cat \
      LIMIT 20 OFFSET %offset%";
  
  var parseFirstResults = function(data){
	  console.log(data);
      var i = 0;
      var cat_len = data.results.bindings.length;
      var domEntry;
	  var poet_cat;
 	  var skip = 0;
	  var ap_skip = 0;
          $first_results = $("#first_results");            
      
      // empty old stuff
      $first_results.empty();
	  	  domEntry = '<li><a href="#american-poets-list"';
		  domEntry += ' onclick="getAmericanPoets(\'' + ap_skip + '\')">';
		  domEntry += 'American Poets</a></li>';
      // fill in
      for(i = 0; i < cat_len; i++){
          poet_cats = data.results.bindings[i];
          poet_cat = poet_cats.poet_cat.value;
		  poet_cat_name = poet_cats.poet_cat_name.value;
          domEntry += '<li><a href="#poet-by-selected-nat-list"';
          domEntry += ' onclick="getPoetsBySelectedNatList(\''+ poet_cat + '\')">';
          domEntry += poet_cat_name;
          domEntry += '</a></li>';
    }
	$first_results.append( $(domEntry) );      
        // refresh style
      $first_results.listview('refresh');
      
      // hide loader
      $.mobile.hidePageLoadingMsg();
  };
  
  var getPoetCats = function(skip){
      // show loader
      $.mobile.showPageLoadingMsg();
      
      if( skip == null || typeof skip == "undefined" ) skip = 0;
      
      var prepQuery = init_query.replace("%offset%", skip);
      var url = "http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=" + escape(prepQuery) + "&format=json";
  
    $.getJSON(url, parseFirstResults);
  }
  
    //
  $(document).ready(function(){
      var skip = 0;
      
      $("#prev").click(function(){
          skip -= 20;
          if( skip < 0 ) skip = 0;
          getPoetCats(skip);
      });
  
      $("#next").click(function(){
          skip += 20;
          getPoetCats(skip);
      });
      
       getPoetCats(skip);
  });
  
  var poets_nat_query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
    PREFIX dcterms: <http://purl.org/dc/terms/>\
    PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>\
    PREFIX dbprop: <http://dbpedia.org/property/>\
    PREFIX dbcat: <http://dbpedia.org/resource/Category:>\
    PREFIX skos: <http://www.w3.org/2004/02/skos/core#> \
    SELECT DISTINCT ?poet ?poet_name ?surname ?poet_wiki_link ?abstract \
    WHERE \
    {  ?poet dcterms:subject dbcat:%poet_cat% ;\
        foaf:name ?poet_name ;\
        foaf:surname ?surname ;\
        dbpedia-owl:abstract ?abstract ;\
        foaf:isPrimaryTopicOf ?poet_wiki_link .\
      FILTER ( lang(?abstract) = \"en\" )\
    } ORDER BY ?surname \
      LIMIT 20 OFFSET %offset%";
	  
    var parseSelectedNatResults = function(data){
    var i = 0;
    var selected_nat_list_len = data.results.bindings.length;
      var domEntry; 
	  var selected_nat_list;
      var $selected_nat_result = $("#selected_nat_result");            
      
      // empty old stuff
      $ap_result.empty();
      // fill in
      for(i = 0; i < ap_list_len; i++){
          poet = data.results.bindings[i];
          poet_name = poet.poet_name.value;
          domEntry = '<li><a href="#poet-details"';
          domEntry += ' onclick="getPoetDetails(\''+ poet_name + '\')">';
          domEntry += poet_name;
          domEntry += '</a></li>';
          $ap_result.append( $(domEntry) );
      }
      
        // refresh style
      $ap_result.listview('refresh');
      
      // hide loader
      $.mobile.hidePageLoadingMsg();
  };
  
	  var getPoetsBySelectedNatList = function(poet_nat_selected){
      // show loader
      $.mobile.showPageLoadingMsg();
      
      var prepQuery = poet_nat_query.replace("%poet_cat%", poet_nat_selected);
      var url = "http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=" + escape(prepQuery) + "&format=json";
  
    $.getJSON(url, parsePoetResults);
	  }
  
	
	
	var american_poets_query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
    PREFIX dcterms: <http://purl.org/dc/terms/>\
    PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>\
    PREFIX dbprop: <http://dbpedia.org/property/>\
    PREFIX dbcat: <http://dbpedia.org/resource/Category:>\
    PREFIX skos: <http://www.w3.org/2004/02/skos/core#> \
    SELECT DISTINCT ?poet ?poet_name ?surname ?poet_wiki_link ?abstract \
    WHERE \
    {  ?poet dcterms:subject dbcat:American_poets ;\
        foaf:name ?poet_name ;\
        foaf:surname ?surname ;\
        dbpedia-owl:abstract ?abstract ;\
        foaf:isPrimaryTopicOf ?poet_wiki_link .\
      FILTER ( lang(?abstract) = \"en\" )\
    } ORDER BY ?surname \
      LIMIT 20 OFFSET %offset%";
  
  var parseApResults = function(data){
      var i = 0;
      var ap_list_len = data.results.bindings.length;
      var domEntry; 
	  var poet;
      var $ap_result = $("#ap_result");            
      
      // empty old stuff
      $ap_result.empty();
      // fill in
      for(i = 0; i < ap_list_len; i++){
          poet = data.results.bindings[i];
          poet_name = poet.poet_name.value;
          domEntry = '<li><a href="#poet-details"';
          domEntry += ' onclick="getPoetDetails(\''+ poet_name + '\')">';
          domEntry += poet_name;
          domEntry += '</a></li>';
          $ap_result.append( $(domEntry) );
      }
      
        // refresh style
      $ap_result.listview('refresh');
      
      // hide loader
      $.mobile.hidePageLoadingMsg();
  };
  
  var apSkip = function() {
  	var ap_skip = 0;
	
		$("#prev_ap").click(function(){
          ap_skip -= 20;
          if( skip < 0 ) skip = 0;
          getAmericanPoets(ap_skip);
      		});
	 	$("#next_ap").click(function(){
          ap_skip += 20;
          getAmericanPoets(ap_skip);
      });
  };
  
  var getAmericanPoets = function(ap_skip){
      // show loader
      $.mobile.showPageLoadingMsg();
      
      if( ap_skip == null || typeof ap_skip == "undefined" ) ap_skip = 0;
      
      var prepQuery = american_poets_query.replace("%offset%", ap_skip);
      var url = "http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=" + escape(prepQuery) + "&format=json";
  
    $.getJSON(url, parseApResults);
  };
  

         var poet_name = "Edgar Allan Poe";
  
           var poetquery = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
    PREFIX dcterms: <http://purl.org/dc/terms/>\
    PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>\
    PREFIX dbprop: <http://dbpedia.org/property/>\
    PREFIX dbcat: <http://dbpedia.org/resource/Category:>\
    PREFIX skos: <http://www.w3.org/2004/02/skos/core#> \
    SELECT DISTINCT ?poet_name ?poet_wiki_link ?abstract ?creative_work_name \
           ?external_links \
    WHERE \
    {  ?poet foaf:name  \"%poet_name%\"@en ;\
            foaf:name ?poet_name ; \
            foaf:surname ?surname ;\
            dbpedia-owl:abstract ?abstract ;\
            foaf:isPrimaryTopicOf ?poet_wiki_link .\
            OPTIONAL { \
            ?creative_work dbpedia-owl:writer ?poet ; \
                           dbprop:title ?creative_work_name . \
        } \
      FILTER ( lang(?abstract) = \"en\" )\
    }";
 
  var parsePoetResults = function(data){
      var i = 0;
      var len = data.results.bindings.length;
      var domEntry;
      var poet_details;
       
      
      // empty old stuff
      $("#result_details").empty();
      // fill in
    
          poet_details = data.results.bindings[i];
          poet_name_selected = poet_details.poet_name.value;
		  poet_wiki_link = poet_details.poet_wiki_link.value;
		  console.log(poet_wiki_link);
          domEntry = '<h1>';
          domEntry += '<a data-rel=\"dialog\" href="';
		  domEntry += poet_wiki_link;
		  domEntry += '">';
          domEntry += poet_name_selected + '</a></h1>';
          domEntry += '<p>' + poet_details.abstract.value + '</p>';
          domEntry += '<ul>';
          for(i=0; i < len; i++)
            if (typeof poet_creative_work != "undefined") {
              {
              domEntry += '<li>' + poet_details.creative_work_name[i].value;
              domEntry += '</li>';
              }
              domEntry += '</ul>';
              }
          $("#result_details").append( $(domEntry) );

      
      // hide loader
      $.mobile.hidePageLoadingMsg();
  };
  
  var getPoetDetails = function(poet_name_selected){
      // show loader
      $.mobile.showPageLoadingMsg();
      
      var prepQuery = poetquery.replace("%poet_name%", poet_name_selected);
      var url = "http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=" + escape(prepQuery) + "&format=json";
  
    $.getJSON(url, parsePoetResults);
  }