<?php

include_once("config.php");
include("common_functions.php");
include_once("lib/xmlDbConnection.class.php");

$id = $_REQUEST["id"];

$terms = $_REQUEST["keyword"];

$exist_args{"debug"} = false;
$xmldb = new xmlDbConnection($exist_args);

$xsl_file = "xslt/article.xsl";
$header_xsl = "xslt/yjallen-dc.xsl";
$header2_xsl = "xslt/dc-htmldc.xsl";
//$xsl_params = array('mode' => "flat", "vol" => $vol);


$query='declare namespace tei="http://www.tei-c.org/ns/1.0";
for $b in /tei:TEI[@xml:id="' . "$id" . '"]';
if ($terms != '') {$query .= "[. |= \"$terms\"]";}
$query .= 'return
<result>
{$b}
</result>';

// run the query 
$xmldb->xquery($query);

$xmldb->xslBind($header_xsl);
$xmldb->xslBind($header2_xsl);
$xmldb->transform();

html_head("Documents", true);

$xmldb->printResult();
print '</head>';
include("web/xml/browse-head.xml");

print '<div class="content">';

print '<h2>Young John Allen Documents</h2>';

$xmldb->xslTransform($xsl_file);
$xmldb->printResult();


?> 
   
</div>
   
<?php
  include("web/xml/footer.xml");
?>


</body>
</html>
