<div class="pager">
<?php
if(!isset($paramname)){
	$paramname = 'page';
}

list($base,$param) = mfwHttp::extractURL(mfwRequest::url());
unset($param[$paramname]);
$baseurl = mfwHttp::composeUrl($base,$param);

if($page>1){
	$url = mfwHttp::composeUrl($baseurl,array($paramname=>$page-1));
	echo "<a class=\"prev\" href=\"{$url}\">Prev</a>\n";
}
else{
	echo "<span class=\"prev\">Prev</span>\n";
}

if($page_max <= 9){
	for($i=1;$i<=$page_max;++$i){
		if($i!=$page){
			$url = mfwHttp::composeUrl($baseurl,array($paramname=>$i));
			echo "<a href=\"{$url}\">{$i}</a>\n";
		}
		else{
			echo "<span>$i</span>\n";
		}
	}
}
elseif($page <= 5){
	for($i=1;$i<=7;++$i){
		if($i!=$page){
			$url = mfwHttp::composeUrl($baseurl,array($paramname=>$i));
			echo "<a href=\"{$url}\">{$i}</a>\n";
		}
		else{
			echo "<span>$i</span>\n";
		}
	}
	$url = mfwHttp::composeUrl($baseurl,array($paramname=>$page_max));
	echo "...\n<a href=\"$url\">$page_max</a>\n";
}
elseif($page > $page_max-5){
	$url = mfwHttp::composeUrl($baseurl,array($paramname=>1));
	echo "<a href=\"$url\">1</a>\n...\n";
	for($i=$page_max-6;$i<=$page_max;++$i){
		if($i!=$page){
			$url = mfwHttp::composeUrl($baseurl,array($paramname=>$i));
			echo "<a href=\"{$url}\">{$i}</a>\n";
		}
		else{
			echo "<span>$i</span>\n";
		}
	}
}
else{
	$url = mfwHttp::composeUrl($baseurl,array($paramname=>1));
	echo "<a href=\"$url\">1</a>\n...\n";
	for($i=$page-2;$i<$page;++$i){
		$url = mfwHttp::composeUrl($baseurl,array($paramname=>$i));
		echo "<a href=\"{$url}\">{$i}</a>\n";
	}
	echo "$page\n";
	for($i=$page+1;$i<=$page+2;++$i){
		$url = mfwHttp::composeUrl($baseurl,array($paramname=>$i));
		echo "<a href=\"{$url}\">{$i}</a>\n";
	}
	$url = mfwHttp::composeUrl($baseurl,array($paramname=>$page_max));
	echo "...\n<a href=\"$url\">$page_max</a>\n";
}
if($page<$page_max){
	$url = mfwHttp::composeUrl($baseurl,array($paramname=>$page+1));
	echo "<a class=\"next\" href=\"{$url}\">Next</a>\n";
}
else{
	echo "<span class=\"next\">Next</span>\n";
}
?>
<form action="<?=$baseurl?>" method="get">
page: <input type="text" size="3" name="<?=$paramname?>" value="<?=$page?>">/<?=$page_max?>
<input type="submit" value="">
</form>
</div>
