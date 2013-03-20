<?php
$anchor = "pull_{$alert['pull']->getId()}";
$linkid = mfwRequest::makeLinkId(mfwRequest::url()."#$anchor");
?>
<div class="alert_block" id="<?=$anchor?>">
<div class="block_head">
<ul class="repo_name">
<li><a href="<?=url("/pulls/repo?repo_id={$alert['repo']->getId()}")?>"><?=$alert['repo']->getProject()?></a></li>
</ul>
<h3><a href="<?=$alert['github_url']?>" target="_blank"><span class="number"><?=$alert['pull']->getNumber()?>:</span> <?=$alert['pull']->getTitle()?></a></h3>
<ul class="info">
<li><span class="tag">Login Name:</span> <?=$alert['pull']->getUser()?></li>
<li><span class="tag">Created At:</span> <?=$alert['pull']->getCreatedAt()?></li>
</ul>
<div class="view_link">[<a href="/pulls/view?pull_id=<?=$alert['pull']->getId()?>&link_id=<?=mfwRequest::makeThisLinkId()?>">詳細</a>]</div>
</div>
<div class="detail">
<ul class="alerts">
<?php foreach($alert['alerts'] as $a):?>
<li><?=$a->getName()?></li>
<?php endforeach?>
</ul>
<ul class="files">
<?php foreach($alert['files'] as $f):?>
<li><?=$f['filename']?>
<ul class="alerts">
<?php foreach($f['alerts'] as $a):?>
<li><?=$a->getName()?></li>
<?php endforeach?>
</ul>
</li>
<?php endforeach?>
</ul>
</div>
</div>
