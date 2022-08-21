exec("~/public_html/MeTube/permis.sh");
$out = shell_exec("touch ~/public_html/MeTube/metube_template/hey.txt");
echo "<pre>$out</pre>";
