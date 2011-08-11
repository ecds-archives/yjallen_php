<?php

include_once("config.php");
include("common_functions.php");
include_once("lib/xmlDbConnection.class.php");

$id = $_REQUEST["id"];

$terms = $_REQUEST["keyword"];

$exist_args{"debug"} = true;
$xmldb = new xmlDbConnection($exist_args);

$xsl_file = "xslt/article.xsl";
$header_xsl = "xslt/yjallen-dc.xsl";
$header2_xsl = "xslt/dc-htmldc.xsl";
//$xsl_params = array('mode' => "flat", "vol" => $vol);


$query='declare namespace tei="http://www.tei-c.org/ns/1.0";
declare option exist:serialize "highlight-matches=all";
for $b in /tei:TEI[@xml:id="' . "$id" . '"]';
if ($terms != '') {$query .= "[ft:query(., \"$terms\")]";}
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
print '<script type="text/javascript">
<!--
function popup(mylink, windowname)
{
if (! window.focus)return true;
var href;
if (typeof(mylink) == "string")
   href=mylink;
else
   href=mylink.href;
window.open(href, windowname, "width=225,height=410,scrollbars=yes");
return false;
}
//-->
</script>';

print '</head>';

print "<body  onLoad='popup(\"flashtat.php?id=$id\", \"Text Analysis\")'>";

include("web/xml/browse-head.xml");

print '<div class="content">';

print '<h2>Young John Allen Documents</h2>';

$xmldb->xslTransform($xsl_file);
$xmldb->printResult();

//print '<a href="flashtat.php?id=' . $id . '" 
  //   onClick="return popup(this, \'text-analysis\')">Analyze this text!</a>';

?> 

</div>
   
<?php
  include("web/xml/footer.xml");
?>


</body>
</html>
