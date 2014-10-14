function loadXMLDoc(doc) {
	if (window.XMLHttpRequest) {
		xhttp=new XMLHttpRequest();
	}
	else{
		xhttp=new ActiveXObject("Microsoft.XMLHTTP");
	}
	xhttp.open("GET",doc,false);
	xhttp.send();
	return xhttp.responseXML;
}