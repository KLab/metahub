<h2><?=$repo->getProject()?>のPull Request</h2>

<ul class="repolist">
<?php foreach($repos as $r):?>
<li<?=($r->getId()==$repo->getid())?' class="current"':''?>><a href="<?=url("/pulls/repo?repo_id={$r->getId()}")?>"><?=$r->getProject()?></a></li>
<?php endforeach?>
<li class="return"><a href="<?=url("/pulls/index")?>">一覧に戻る</a>
</ul>

<h3 class="repoinfo">リポジトリ情報</h3>
<p class="repoinfo">
github: <a href="https://github.com/<?=$github_project_owner?>/<?=$repo->getName()?>">https://github.com/<?=$github_project_owner?>/<?=$repo->getName()?></a>
<a class="edit" href="<?=url("/pulls/repoEdit?repo_id={$repo->getId()}")?>">編集</a>
</p>

<?php if($page_max){echo block('pager');}?>

<?php if(empty($alerts)):?>
<p>ありません</p>
<?php endif?>
<?php foreach($alerts as $alert):?>
<?=block('pull_alert',array('alert'=>$alert))?>
<?php endforeach?>

<?php if($page_max){echo block('pager');}?>
