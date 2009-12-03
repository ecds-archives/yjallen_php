<?php
include_once("config.php");
include_once("lib/xmlDbConnection.class.php");

$exist_args{"debug"} = false;

$db = new xmlDbConnection($exist_args);

global $title;
global $abbrev;
global $collection;


$id = $_GET["id"]; 
$keyword = $_GET["keyword"];


$htmltitle = "The Young John Allen Papers";


// what should be displayed here?  for sc: article title, author, date

// use article query with context added
// note: using |= instead of &= because we want context for any of the
// keyword terms, whether they appear together or not
$xquery = "declare namespace tei='http://www.tei-c.org/ns/1.0';
declare option exist:serialize 'highlight-matches=all';
let \$doc := /tei:TEI[@xml:id = \"$id\"]
return 
<item>
{\$doc/@xml:id}
{\$doc//tei:titleStmt//tei:title}
{\$doc//tei:titleStmt//tei:author/tei:name}
{\$doc//tei:sourceDesc//tei:date}
<context>
{for \$c in \$doc//*[. |= \"$keyword\"]
   return if (name(\$c) = 'tei:hi') then \$c/..[. |= \"$keyword\"] else  \$c }</context>
</item>";


/* this is one way to specify context nodes  (filter based on the kinds of nodes to include)
  <context>{(\$a//p|\$a//titlePart|\$a//q|\$a//note)[. &= '$keyword']}</context>
   above is another way-- allow any node, but if the node is a <hi>, return parent instead
   (what other nodes would need to be excluded? title? others?)
*/

$db->xquery($xquery);
//$doctitle = $db->findnode("title");
// truncate document title for html header
//$doctitle = str_replace(", an electronic edition", "", $doctitle);


print "$doctype
<html>
 <head>
    <title>$htmltitle : $doctitle : Keyword in Context</title>
    <link rel='stylesheet' type='text/css' href='web/css/yjallen.css'>";

include("xml/browse-head.xml");

print "<div class='content'>
<div class='title'><a href='index.html'>$title</a></div>";

$xsl_params = array("url_suffix" => "keyword=$keyword");

$db->xslBind("xslt/kwic-towords.xsl");
$db->xslBind("xslt/kwic.xsl", $xsl_params);

$db->transform();
$db->printResult();

include("web/xml/footer.xml");
?>
