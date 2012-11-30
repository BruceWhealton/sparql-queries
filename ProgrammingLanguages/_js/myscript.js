    var query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
PREFIX dcterms: <http://purl.org/dc/terms/>\
PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>\
PREFIX dbprop: <http://dbpedia.org/property/>\
PREFIX dbcat: <http://dbpedia.org/resource/Category:>\
PREFIX skos: <http://www.w3.org/2004/02/skos/core#> \
PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> \
SELECT DISTINCT ?an ?bn \
WHERE \
{ ?a rdf:type dbpedia-owl:ProgrammingLanguage ;\
foaf:name ?an ;\
dbpedia-owl:influencedBy ?b .\
?b foaf:name ?bn . \
} ORDER BY ?an \
LIMIT 30 OFFSET %offset%";
  
  var parseResults = function(data){
      console.log(data);
      var i = 0,
          len = data.results.bindings.length,
          domEntry, lang_result,
          $result = $("#list-result");
      
      // empty old stuff
      $("#list-result").empty();
      // fill in
      for(i = 0; i < len; i++){
          lang_result = data.results.bindings[i];
          a_name = lang_result.an.value;
b_name = lang_result.bn.value;
          domEntry = '<li><a href="#lang-details"';
          domEntry += ' onclick="getLangDetails(\''+ a_name + '\')">';
          domEntry += a_name;
domEntry += '</a>';
domEntry += ' was influenced by ';
domEntry += '<a href="#lang-details"';
domEntry += ' onclick="getLangDetails(\''+ b_name + '\')">';	
domEntry += b_name;
          domEntry += '</a></li>';
          $("#list-result").append( $(domEntry) );
      }
      
        // refresh style
      $("#list-result").listview('refresh');
      
      // hide loader
      $.mobile.hidePageLoadingMsg();
  };
  
  var getLangs = function(skip){
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
          getLangs(skip);
      });
  
      $("#next").click(function(){
          skip += 30;
          getLangs(skip);
      });
      
       getLangs(skip);
  });
  
  
           var langquery = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>\
PREFIX dcterms: <http://purl.org/dc/terms/>\
PREFIX dbpedia-owl: <http://dbpedia.org/ontology/>\
PREFIX dbprop: <http://dbpedia.org/property/>\
PREFIX dbcat: <http://dbpedia.org/resource/Category:>\
PREFIX skos: <http://www.w3.org/2004/02/skos/core#> \
SELECT DISTINCT ?an ?abstract ?wiki_link \
WHERE \
{ ?a foaf:name \"%lang_name%\"@en ;\
foaf:name ?an ;\
rdf:type dbpedia-owl:ProgrammingLanguage ;\
dbpedia-owl:abstract ?abstract ;\
foaf:isPrimaryTopicOf ?wiki_link .\
FILTER ( lang(?abstract) = \"en\" )\
}";
 
  var parseLangResults = function(data){
     console.log(data);
      var i = 0;
      var len = data.results.bindings.length;
      var domEntry;
      var lang_details;
       
      
      // empty old stuff
      $("#lang-result-details").empty();
      // fill in
result0 = data.results.bindings[0];
     domEntry = '<h1>';
domEntry += '<a data-rel=\"dialog\" href="';
domEntry += result0.wiki_link.value;
domEntry += '">';
domEntry += result0.an.value;
domEntry += '</a>';
domEntry += '<p>';
domEntry += result0.abstract.value;
domEntry += '</p>';
      for(i = 0; i < len; i++){
          lang_result = data.results.bindings[i];
          //b_name = lang_result.bn.value;
//c_name = lang_result.cn.value;
          //domEntry += '<ul><li>This language was influenced by: ';
          //domEntry += b_name;
//domEntry += 'and this language influenced ';
//domEntry += c_name;
//domEntry += ' onclick="getLangDetails(\''+ b_name + '\')">';
//domEntry += b_name;
          //domEntry += '</a></li></ul>';
          $("#lang-result-details").append( $(domEntry) );
      }

      
      // hide loader
      $.mobile.hidePageLoadingMsg();
  };
  
  var getLangDetails = function(lang_name_selected){
      // show loader
      $.mobile.showPageLoadingMsg();
      
      var prepQuery = langquery.replace("%lang_name%", lang_name_selected);
      var url = "http://dbpedia.org/sparql?default-graph-uri=http%3A%2F%2Fdbpedia.org&query=" + escape(prepQuery) + "&format=json";
  
    $.getJSON(url, parseLangResults);
  }