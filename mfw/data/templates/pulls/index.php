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
<?=block('pull_alert',array('alert'=>$alert))?>
<?php endforeach?>

<?php if($page_max){echo block('pager');}?>
