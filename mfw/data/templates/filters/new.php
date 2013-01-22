<h2>フィルタ作成</h2>


<form action="<?=url('/filters/save')?>" method="post">

<table>
<th class="left">Name</th><td><input style="width:99%" type="edit" name="name" value=""></td>
</tr>
<th class="left">Target</th>
<td>
<select name="target">
<option value="file:filename">ファイル名</option>
<option value="file:patch">変更差分</option>
<option value="pull:user:login">ログイン名</option>
</select>
</td>
<input type="hidden" name="id" value="">
</tr>
<tr>
<th class="left">Pattern</th>
<td><textarea name="pattern"></textarea></td>
</tr>
<tr>
<th class="left"></th>
<td><input type="submit" name="submit" value="save"></td>
</tr>
</table>
</form>

