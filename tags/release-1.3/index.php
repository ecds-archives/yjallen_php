<?php

include_once("config.php");
include_once("lib/xmlDbConnection.class.php");
include("common_functions.php");

$id = $_REQUEST["id"];

$exist_args{"debug"} = false;
$xmldb = new xmlDbConnection($exist_args);

html_head("Browse", true);

include("web/xml/browse-head.xml");

print '<div class="content">';

//print '<h2>Young John Allen Papers</h2>';

// query  p5 -- eXist


$query = 'declare namespace tei="http://www.tei-c.org/ns/1.0";
for $a in /tei:TEI
order by $a//tei:title/tei:date/@when
return 
<result>
{$a/@xml:id}
{$a/tei:teiHeader//tei:titleStmt/tei:title}
    {$a/tei:teiHeader//tei:titleStmt/tei:author/tei:name//tei:sic}
</result>';

$xsl_file = "xslt/browse.xsl";
//$xsl_params = array('mode' => "flat", "vol" => $vol);

$maxdisplay = "100"; //show all the issues
$position = "1"; //start here

// run the query 
$xmldb->xquery($query, $position, $maxdisplay);
$xmldb->xslTransform($xsl_file);
$xmldb->printResult();

include("web/xml/footer.xml");
?> 
   
</div>
   



</body>
</html>
