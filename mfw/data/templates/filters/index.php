<h2>フィルタ一覧</h2>

<table class="filterlist">
<tr class="head">
<th class="filter_id">ID</th><th class="filter_enable">Enable</th><th class="filter_name">Name</th><th class="filter_target">Target</th><th>pattern</th>
</tr>
<?php foreach($filters as $f):?>
<tr class="items <?=(($f->isEnable())?'enable':'disable')?>">
<td class="filter_id"><?=$f->getId()?></td>
<td class="filter_enable">
<?php if($f->isEnable()):?>
<span class="enable">有効</span>
<?php else:?>
<span class="disable">無効</span>
<?php endif?>
</td>
<td id="filter_name_<?=$f->getId()?>" class="filter_naem"><a href="<?=url("/filters/edit?id={$f->getId()}")?>" title="編集"><?=htmlspecialchars($f->getName())?></a></td>
<td id="filter_target_<?=$f->getId()?>" class="filter_target" ><?php
	switch($f->getTarget()){
	case Filter::TARGET_FILENAME:
		echo 'ファイル名';
		break;
	case Filter::TARGET_FILEPATCH:
		echo '変更差分';
		break;
	case Filter::TARGET_USERNAME:
		echo 'ログイン名';
		break;
	default:
		echo htmlspecialchars($f->getTarget()), '?';
	}
?></td>
<td id="filter_pattern_<?=$f->getId()?>" class="filter_pattern"><pre><?=htmlspecialchars($f->getPattern())?></pre></td>
</tr>
<?php endforeach?>
<tr>
<td> </td>
<td class="filter_add" colspan="4">
<form action="<?=url('/filters/new')?>" method="get"><input type="submit" value="追加"></form>
</td>
</tr>
</table>

