<h2><?=$repo->getProject()?>編集</h2>


<form action="<?=url('/pulls/repoSave')?>" method="post">
<input type="hidden" name="repo_id" value="<?=$repo->getId()?>">

<table>

<tr>
<th class="left">ID</th>
<td><?=$repo->getId()?></td>
</tr>

<tr>
<th class="left">Repository</th>
<td>https://github.com/<?=$github_project_owner?>/<input style="width:15em" type="edit" name="name" value="<?=htmlspecialchars($repo->getName())?>"></td>
</tr>

<tr>
<th class="left">ProjectName</th>
<td><input type="edit" name="project" value="<?=htmlspecialchars($repo->getProject())?>"></td>
</tr>

<tr>
<th class="left"></th>
<td><input type="submit" name="submit" value="save"></td>
</tr>

</table>
</form>

<p class="return_link"><a href="<?=url("/pulls/repo?repo_id={$repo->getId()}")?>">戻る</a></p>

