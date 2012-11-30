var query = "PREFIX foaf: <http://xmlns.com/foaf/0.1/> \
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
  } ORDER BY ?poet \
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
        domEntry = '<li><a href="#">';
        domEntry += poet.poet_name.value;
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
        skip -= 10;
        if( skip < 0 ) skip = 0;
        getPoets(skip);
    });

    $("#next").click(function(){
        skip += 10;
        getPoets(skip);
    });
    
     getPoets(skip);
});
​
​


function listPosts(data) {
	var output='<ul data-role="listview" data-filter="true">';
	$.each(data.posts,function(key,val) {
	
		var tempDiv = document.createElement("tempDiv");
		tempDiv.innerHTML = val.excerpt;
		$("a",tempDiv).remove();
		var excerpt = tempDiv.innerHTML;	
	
		output += '<li>';
		output += '<a href="#blogpost" onclick="showPost(' + val.id + ')">';
		output += '<h3>' + val.title + '</h3>';
		output += (val.thumbnail) ? '<img src="' + val.thumbnail + '"alt="' + val.title + '"/>': '<img src="images/viewsourcelogo.png" alt="View Source Logo" />';
		output += '<p>' + excerpt + '</p>';
		output += '</a>';
		output += '</li>';
	}); // go through each post
	output+='</ul>';
	$('#postlist').html(output);
} // lists all the posts

function showPost(id) {
	$.getJSON('http://iviewsource.com/?json=get_post&post_id=' + id + '&callback=?',function(data) {
		var output='';
		output += '<h3>' + data.post.title + '</h3>';
		output += data.post.content;
		$('#mypost').html(output);
	}); // get JSON Data frm Stories
} //showPost

function listVideos(data) {
	
	var output ='';
	for ( var i=0; i<data.feed.entry.length; i++) {

		var title = data.feed.entry[i].title.$t;
		var thumbnail = data.feed.entry[i].media$group.media$thumbnail[0].url;
		var description = data.feed.entry[i].media$group.media$description.$t;
		var id = data.feed.entry[i].id.$t.substring(38);
		
		var blocktype = ((i % 2)===1) ? 'b': 'a';
		
		output += '<div class="ui-block-' + blocktype + '">';

		output += '<a href="#videoplayer" data-transition="fade" onclick="playVideo(\'' +  id +'\',\'' + title + '\',\'' + escape(description) + '\')">';
		output += '<h3 class="movietitle">' + title + '</h3>';
		output += '<img src="' + thumbnail + '" alt="' + title + '" />';
		output +="</a>";
		output +="</div>";
	}
	
	$('#videolist').html(output);
}

function playVideo(id, title, description) {
	var output ='<iframe src="http://www.youtube.com/embed/'+ id +'?wmode=transparent&amp;HD=0&amp;rel=0&amp;showinfo=0&amp;controls=1&amp;autoplay=1" frameborder="0" allowfullscreen></iframe>';
	output += '<h3>' + title + '</h3>';
	output += '<p>' + unescape(description) + '</p>';
	$('#myplayer').html(output);
}

function jsonFlickrFeed(data) {
	console.log(data);
	var output='';
	
	for (var i = 0; i < data.items.length; i++) {
		var title = data.items[i].title;
		var link = data.items[i].media.m.substring(0, 56);
		var blocktype =
			((i%3)===2) ? 'c':
			((i%3)===1) ? 'b':
			'a';
		output += '<div class="ui-block-' + blocktype + '">';
		output += '<a href="#showphoto" data-transition="fade" onclick="showPhoto(\''+ link + '\',\'' + title + '\')">';
		output += '<img src="' + link + '_q.jpg" alt="' + title + '" />';
		output += '</a>';
		output += '</div>';
	} // go through each photo
	$('#photolist').html(output);
} //jsonFlickrFeed

function showPhoto(link, title) {
	var output='<a href="#photos" data-transition="fade">';
	output += '<img src="' + link + '_b.jpg" alt="' + title +'" />';
	output += '</a>';
	$('#myphoto').html(output);
}

function listTweets(data) {
	console.log(data);
	var output = '<ul data-role="listview" data-theme="a">';
	$.each(data, function(key, val) {
		var text = data[key].text;
		var thumbnail = data[key].user.profile_image_url;
		var name = data[key].user.name;
		
		text=text.replace(/[A-Za-z]+:\/\/[A-Za-z0-9-_]+\.[A-Za-z0-9-_:%&~\?\/.=]+/g,function(i) {
		var url=i.link(i);
		return url;	
		});
		
		text=text.replace(/[@]+[A-Za-z0-9-_]+/g,function(i) {
			var item = i.replace("@",'');
			var url = i.link("http://twitter.com/" + item);
			return url;
			});
			
		text=text.replace(/[#]+[A-Za-z0-9-_]+/g,function(i) {
			var item = i.replace("#", '%23');
			var url = i.link("http://search.twitter.com/search?q="+item);
			return url;
			});
		
		output += '<li>';
		output += '<img src="' + thumbnail + '" alt="Photo of ' + name + '">';
		output += '<div>' + text + '</div>';
		output += '</li>';
	});  // Go through each tweet
	output += '</ul>';
	$('#tweetlist').html(output);
}
