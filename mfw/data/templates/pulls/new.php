<h2>githubリポジトリ登録</h2>


<form action="<?=url('/pulls/add')?>" method="post">

<table>

<tr>
<th class="left">Repository</th>
<td>https://github.com/<?=$github_project_owner?>/<input style="width:15em" type="edit" name="name" value=""></td>
</tr>

<tr>
<th class="left">ProjectName</th>
<td><input type="edit" name="project" value=""></td>
</tr>

<tr>
<th class="left"></th>
<td><input type="submit" name="submit" value="save"></td>
</tr>

</table>
</form>

<p class="return_link"><a href="<?=url("/pulls/index")?>">戻る</a></p>

