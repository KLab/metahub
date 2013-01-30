<link type="text/css" rel="stylesheet" href="/vendor/syntaxhighlighter/styles/shCore.css">
<link type="text/css" rel="stylesheet" href="/vendor/syntaxhighlighter/styles/shCoreDefault.css">
<script type="text/javascript" src="/vendor/syntaxhighlighter/scripts/shCore.js"></script>
<script type="text/javascript" src="/vendor/syntaxhighlighter/scripts/shBrushPhp.js"></script>
<?php
$returl = mfwRequest::getReturnUrl();
if(!$returl){
	$returl = url('/pulls/index');
}
?>
<h2>[<?=$repo->getProject()?>] <?=$pull->getNumber()?>:<?=$pull->getTitle()?></h2>

<ul class="repolist">
<?php foreach($repos as $r):?>
<li><a href="<?=url("/pulls/repo?repo_id={$r->getId()}")?>"><?=$r->getProject()?></a></li>
<?php endforeach?>
<li class="return"><a href="<?=$returl?>">戻る</a>
</ul>
<br>

<div class="alert_block">
<div class="block_head">
<ul class="repo_name">
<li><a href="<?=url("/pulls/repo?repo_id={$repo->getId()}")?>"><?=$repo->getProject()?></a></li>
</ul>
<h3><a href="<?=$rawpull['_links']['html']['href']?>" target="_blank"><span class="number"><?=$pull->getNumber()?>:</span> <?=$pull->getTitle()?></a></h3>
<ul class="info">
<li><span class="tag">Login Name:</span> <?=$pull->getUser()?></li>
<li><span class="tag">Created At:</span> <?=$pull->getCreatedAt()?></li>
</ul>
</div>

<div class="detail">
<div class="description">
<?=$rawpull['body']?htmlspecialchars($rawpull['body']):'No description given.'?>
</div>

<h4>comments</h4>
<ul class="comments">
<?php foreach($comments as $comment):?>
  <li>
    <?=htmlspecialchars($comment['body'])?>
    <span class="user">
      <img src="<?=$comment['user']['avatar_url']?>" width="16" height="16">
      <?=htmlspecialchars($comment['user']['login'])?>
    </span>
  </li>
<?php endforeach?>
</ul>

</div>
</div>

<?php foreach($files as $file):?>
<div class="file_block">
<div class="block_head">
<h3><?=$file['filename']?></h3>
</div>
<div class="detail">
<pre class="brush: php">
<?=preg_replace('/(^|\n)(@@ [^@]* @@)/',"$1$2\n",$file['patch'])?>
</pre>
</div>
</div>
<?php endforeach?>

<div class="return">
<a href="<?=$returl?>">戻る</a>
</div>

<script type="text/javascript">
SyntaxHighlighter.all()
</script>
