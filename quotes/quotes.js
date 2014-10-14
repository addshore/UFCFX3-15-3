// Used in quotes.php outputJsHtml

document.write( "<h2>Quote Collection</h2>\n<p>Please see quotes below.</p>\n" );
document.write( "<table border=\"1\">\n" );

//TODO dynamic headers?
document.write( "<tr bgcolor=\"#9acd32\">\n" );
document.write( '<th style="text-align:left">text</th>');
document.write( '<th style="text-align:left">source</th>');
document.write( '<th style="text-align:left">dob-dod</th>');
document.write( '<th style="text-align:left">wplink</th>');
document.write( '<th style="text-align:left">wpimg</th>');
document.write( '<th style="text-align:left">category</th>' );
document.write( "</tr>\n" );

xmlDoc=loadXMLDoc("./quotes.php?format=xml");
for (var i = 0; i < xmlDoc.getElementsByTagName("text").length; i++) {
	document.write ("<tr>\n");
	document.write( "<td>" + xmlDoc.getElementsByTagName("text")[i].childNodes[0].nodeValue + "</td>" );
	document.write( "<td>" + xmlDoc.getElementsByTagName("source")[i].childNodes[0].nodeValue + "</td>" );
	document.write( "<td>" + xmlDoc.getElementsByTagName("dob-dod")[i].childNodes[0].nodeValue + "</td>" );
	document.write( "<td>" + "<a href=\"" + xmlDoc.getElementsByTagName("wplink")[i].childNodes[0].nodeValue + "\">" );
	document.write( xmlDoc.getElementsByTagName("source")[i].childNodes[0].nodeValue + "</a></td>" );
	document.write( "<td>" + "<img src=\"" + xmlDoc.getElementsByTagName("wpimg")[i].childNodes[0].nodeValue + "\" width=\"110px\" height=\"110px\">" + "</td>" );
	document.write( "<td>" + xmlDoc.getElementsByTagName("category")[i].childNodes[0].nodeValue + "</td>" );
	document.write ("</tr>\n");
}

document.write( "</table>\n<p>This is some footer!</p>\n" );