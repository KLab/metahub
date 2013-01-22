<h2>Pull Request一覧</h2>

<ul class="repolist">
<?php foreach($repos as $r):?>
<li><a href="<?=url("/pulls/repo?repo_id={$r->getId()}")?>"><?=$r->getProject()?></a></li>
<?php endforeach?>
<li class="add"><a href="<?=url("/pulls/new")?>">追加</a>
</ul>

<?php if($page_max){echo block('pager');}?>

<?php if(empty($alerts)):?>
<p>ありません</p>
<?php endif?>
<?php foreach($alerts as $alert):?>
<div class="alert_block">
<div class="block_head">
<ul class="repo_name">
<li><a href="<?=url("/pulls/repo?repo_id={$alert['repo']->getId()}")?>"><?=$alert['repo']->getProject()?></a></li>
</ul>
<h3><a href="<?=$alert['github_url']?>" target="_blank"><span class="number"><?=$alert['pull']->getNumber()?>:</span> <?=$alert['pull']->getTitle()?></a></h3>
<ul class="info">
<li><span class="tag">Login Name:</span> <?=$alert['pull']->getUser()?></li>
<li><span class="tag">Created At:</span> <?=$alert['pull']->getCreatedAt()?></li>
</ul>
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
<?php endforeach?>

<?php if($page_max){echo block('pager');}?>
