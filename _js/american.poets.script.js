    var am_poets_query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
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
  
  var parseAmPoetResults = function(data){
      console.log(data);
      var i = 0,
          len = data.results.bindings.length,
          domEntry, poet,
          $result = $("#american-poets-list-result");            
      
      // empty old stuff
      //$("american-poets-list-result").empty();
      // fill in
      for(i = 0; i < len; i++){
          poet = data.results.bindings[i];
          poet_name = poet.poet_name.value;
          domEntry = '<li><a href="#american-poet-details"';
          domEntry += ' onclick="getPoetDetails(\''+ poet_name + '\')">';
          domEntry += poet_name;
          domEntry += '</a></li>';
          $("#american-poets-list-result").append( $(domEntry) );
      }
      
        // refresh style
     // $result.listview('refresh');
      
      // hide loader
      $.mobile.hidePageLoadingMsg();
  };
  
  var getAmPoets = function(skip){
      // show loader
      $.mobile.showPageLoadingMsg();
      
      if( skip == null || typeof skip == "undefined" ) skip = 0;
      
      var prepQuery = am_poets_query.replace("%offset%", skip);
      var url = "http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=" + escape(prepQuery) + "&format=json";
  
    $.getJSON(url, parseResults);
  }
  
  //
  $(document).ready(function(){
      var skip = 0;
      
      $("#prev").click(function(){
          skip -= 30;
          if( skip < 0 ) skip = 0;
          getAmPoets(skip);
      });
  
      $("#next").click(function(){
          skip += 30;
          getAmPoets(skip);
      });
      
       getAmPoets(skip);
  });

  
     var am_poet_detail_query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
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
     console.log(data);
      var i = 0;
      var len = data.results.bindings.length;
      var domEntry;
      var poet_details;
       
      
      // empty old stuff
      $("#american_poet_result_details").empty();
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
          $("#american_poet_result_details").append( $(domEntry) );

      
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