<h1>Error</h1>
<p>
<?=$message?>
</p>
<p class="return">
<?if($link_url):?>
<a href="<?=$link_url?>"><?=$link_msg?></a>
<?else:?>
<a href="<?=url('/top/index')?>">top</a>
<?endif?>
</p>
