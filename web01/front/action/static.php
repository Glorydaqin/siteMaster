<?php

$script_uri = addslashes($script_uri);
$sql = "select * from static where url='{$script_uri}'";
$info = $GLOBALS['db']->getFirstRow($sql);

if(!$info){
    temporarily_header_401();
}

$tpl->assign("info",$info);
$meta = array(
    "title"=>"{$info['title']} - ",
    "description"=>"",
    "keywords"=>"",
);
$tpl->assign("meta",$meta);

$content=$tpl->render('static.php');
//$mainContent = $cache_obj->set_cache($cacheKey, $content);

echo $tpl->render('static.php');
exit();