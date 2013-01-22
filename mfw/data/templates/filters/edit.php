<h2>フィルタ編集</h2>

<form action="<?=url('/filters/save')?>" method="post">
<input type="hidden" name="id" value="<?=$filter->getId()?>">

<table>
<tr>
<th class="left">ID</th>
<td><?=$filter->getId()?></td>
</tr>
<tr>
<th class="left">Enable</th>
<td>
<label><input type="radio" name="enable" value="1"<?=($filter->isEnable())?' checked="checked"':''?>><span class="enable">有効</span></input></label>
<label><input type="radio" name="enable" value="0"<?=(!$filter->isEnable())?' checked="checked"':''?>><span class="disable">無効</span></input></label>
</td>
</tr>
<tr>
<th class="left">Name</th>
<td><input style="width:99%" type="edit" name="name" value="<?=htmlspecialchars($filter->getName())?>"></td>
</tr>
<tr>
<th class="left">Target</th>
<td>
<select name="target">
<option value="file:filename" <?=($filter->getTarget()=='file:filename')?'selected="selected"':''?>>ファイル名</option>
<option value="file:patch" <?=($filter->getTarget()=='file:patch')?'selected="selected"':''?>>変更差分</option>
<option value="pull:user:login" <?=($filter->getTarget()=='pull:user:login')?'selected="selected"':''?>>ログイン名</option>
</select>
</td>
</tr>
<tr>
<th class="left">Pattern</th>
<td><textarea name="pattern"><?=htmlspecialchars($filter->getPattern())?></textarea></td>
</tr>
<tr>
<th class="left"></th>
<td><input type="submit" name="submit" value="save"></td>
</tr>
</table>
</form>

