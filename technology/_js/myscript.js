    var query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
    PREFIX dcterms: <http://purl.org/dc/terms/>\
    PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>\
    PREFIX dbprop: <http://dbpedia.org/property/>\
    PREFIX dbcat: <http://dbpedia.org/resource/Category:>\
    PREFIX skos: <http://www.w3.org/2004/02/skos/core#> \
    SELECT DISTINCT ?term ?term_label ?abstract ?term_wiki_link  \
    WHERE \
    {  ?term dcterms:subject dbcat:Markup_languages ;\
        rdfs:label ?term_label ;\
        dbpedia-owl:abstract ?abstract ;\
        foaf:isPrimaryTopicOf ?term_wiki_link .\
      FILTER ( lang(?abstract) = \"en\" )\
	  FILTER ( lang(?term_label) = \"en\" )\
    } ORDER BY ?term_label \
      LIMIT 30 OFFSET %offset%";
  
  var parseResults = function(data){
      var i = 0,
          len = data.results.bindings.length,
          domEntry, term_data,
          $result = $("#result");            
      
      // empty old stuff
      $("#result").empty();
      // fill in
      for(i = 0; i < len; i++){
          term_data = data.results.bindings[i];
		  term = term_data.term.value;
          term_label = term_data.term_label.value;
		  domEntry = '<ul>';
          domEntry += '<li><a href="#markup-language-details"';
          domEntry += ' onclick="getTermDetails(\''+ term_label + '\')">';
          domEntry += term_label;
          domEntry += '</a></li>';
		  domEntry += '</ul>';
          $("#result").append( $(domEntry) );
      }
      
        // refresh style
      //$result.listview('refresh');
      
      // hide loader
      $.mobile.hidePageLoadingMsg();
  };
  
  var getTerms = function(skip){
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
          skip -= 30;
          if( skip < 0 ) skip = 0;
          getTerms(skip);
      });
  
      $("#next").click(function(){
          skip += 30;
          getTerms(skip);
      });
      
       getTerms(skip);
  });
 
  
       var term_details_query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
    PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
    PREFIX dcterms: <http://purl.org/dc/terms/>\
    PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>\
    PREFIX dbprop: <http://dbpedia.org/property/>\
    PREFIX dbcat: <http://dbpedia.org/resource/Category:>\
    PREFIX skos: <http://www.w3.org/2004/02/skos/core#> \
    SELECT DISTINCT ?term_label ?abstract ?term_wiki_link \
    WHERE \
    {  ?term foaf:name  \"%term_label%\"@en ;\
            rdfs:label ?term_label ; \
            dbpedia-owl:abstract ?abstract ;\
            foaf:isPrimaryTopicOf ?term_wiki_link .\
      FILTER ( lang(?abstract) = \"en\" )\
	  FILTER ( lang(?term_label) = \"en\" )\
    }";
 
  var parseTermResults = function(data){
     console.log(data);
      var i = 0;
      var len = data.results.bindings.length;
      var domEntry;
      var term_details;
       
      
      // empty old stuff
     // $("#markup_language_result_details").empty();
      // fill in
    
          term_details = data.results.bindings[i];
          term_selected = term_details.term_label.value;
		  term_wiki_link = term_details.term_wiki_link.value;
		  console.log(term_wiki_link);
          domEntry = '<h1>';
          domEntry += '<a data-rel=\"dialog\" href="';
		  domEntry += term_wiki_link;
		  domEntry += '">';
          domEntry += term_selected + '</a></h1>';
          domEntry += '<p>' + term_details.abstract.value + '</p>';
          $("#markup_language_result_details").append( $(domEntry) );

      
      // hide loader
      $.mobile.hidePageLoadingMsg();
  };
  
  var getTermDetails = function(term_label_selected){
      // show loader
      $.mobile.showPageLoadingMsg();
      
      var prepQuery = term_details_query.replace("%term_label%", term_label_selected);
      var url = "http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=" + escape(prepQuery) + "&format=json";
  
    $.getJSON(url, parseTermResults);
  }