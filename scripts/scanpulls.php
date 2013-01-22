<?php
require_once(dirname(__FILE__).'/../mfw/initialize.php');
require_once(APP_ROOT.'/core/vendor/optionparse.php');
require_once(APP_ROOT.'/model/Repository.php');
require_once(APP_ROOT.'/model/Filter.php');
require_once(APP_ROOT.'/model/PullRequest.php');
require_once(APP_ROOT.'/model/PullRequestAlert.php');

mfwServerEnv::setEnv('metahub_vm');

$owner = trim(file_get_contents(APP_ROOT.'/../apiauth/github_project_owner'));

// oauth2 access token.
$token = trim(file_get_contents(dirname(__FILE__).'/../apiauth/github_accesstoken'));

$parser = new Optionparse(array(
	'description' => 'scan pullrequests',
	'arguments' => '[repository ...]',
	));

$parser->addOption('help',array(
	'short_name' => '-h',
	'long_name' => '--help',
	'description' => 'show this help message',
	));

$options = $parser->parse();


if($options['help']){
	$parser->displayUsage();
	exit(0);
}


/*----------------------------------------------------------------------------
 * repository一覧
 */
if(!empty($options['_arguments_'])){
	$repolist = RepositoryDb::selectSetByNames($options['_arguments_']);
}
else{
	$repolist = RepositoryDb::selectAll();
}
if(empty($repolist)){
	echo "no repository\n";
	exit(-1);
}

$repos = array();
foreach($repolist as $r){
	$repos[$r->getId()] = $r;
}
unset($repolist);


/*----------------------------------------------------------------------------
 * pull request一覧を取得
 */
$pulllists = array(); ///< pull request一覧
$cursorlist = array(); ///< 読み取った最新のpull request numberのリスト
foreach($repos as $repo){
	$cursor_before = $repo->getCursor();
	$cursor = $cursor_before;
	echo "scan {$repo->getName()} (cursor:{$cursor_before})\n";

	// open pull-requests
	$baseurl = "https://api.github.com/repos/$owner/{$repo->getName()}/pulls";
	$params = array(
		'access_token' => $token,
		);
	$pulls = mfwHttp::get(mfwHttp::composeURL($baseurl,$params));
	if(!$pulls){
		continue;
	}
	$pulls = json_decode($pulls,true);

	foreach($pulls as $p){
		if($p['number']>$cursor_before){
			$cursor = max($p['number'],$cursor);
			$pulllists[$repo->getId()][$p['number']] = $p;
		}
	}

	// closed pull-requests
	$params['state'] = 'closed';
	$pulls = mfwHttp::get(mfwHttp::composeURL($baseurl,$params));
	if(!$pulls){
		unset($pulllists[$repo->getId()]);
		continue;
	}
	$pulls = json_decode($pulls,true);
	foreach($pulls as $p){
		if($p['number']>$cursor_before){
			$cursor = max($p['number'],$cursor);
			$pulllists[$repo->getId()][$p['number']] = $p;
		}
	}
	$cursorlist[$repo->getId()] = $cursor;
}
unset($params);
unset($pulls);

/*----------------------------------------------------------------------------
 * filter準備
 */
$filters = FilterDb::selectEnable();
$filefilters = $filters->filterForFiles();
$pullfilters = $filters->filterForPulls();
unset($filters);

/*----------------------------------------------------------------------------
 * pull requestの中身を精査
 */
$alterts = array();
$alerted_pulls = array();
foreach($pulllists as $repo_id => $pulls){
	$repo = $repos[$repo_id];
	echo "repository: {$repo->getName()}\n";

	// number昇順
	usort($pulls,function($a,$b){return ($a['number']-$b['number']);});

	foreach($pulls as $num => $p){
		echo " {$p['number']} {$p['user']['login']}: {$p['title']}\n";

		$url = "https://api.github.com/repos/{$owner}/{$repo->getName()}/pulls/{$p['number']}/files";
		$params = array(
			'access_token' => $token,
			);
		$files = mfwHttp::get(mfwHttp::composeURL($url,$params));
		$files = json_decode($files,true);

		foreach($pullfilters as $filter){
			if($filter->test($p)){
				echo "      match pull filter:{$filter->getId()}\n";
				$alerts[$repo->getId()][] = array(
					'filter_id' => $filter->getId(),
					'repo_id' => $repo->getId(),
					'number' => $p['number'],
					);
				$alerted_pulls[$repo->getId()][$p['number']] = $p;
			}
		}

		foreach($files as $file){
			foreach($filefilters as $filter){
				if($filter->test($file)){
					echo "      match file filter:{$filter->getId()}\n";
					$alerts[$repo->getId()][] = array(
						'filter_id' => $filter->getId(),
						'repo_id' => $repo->getId(),
						'number' => $p['number'],
						'filename' => $file['filename'],
						);
					$alerted_pulls[$repo->getId()][$p['number']] = $p;
				}
			}
		}
	}
}


/*----------------------------------------------------------------------------
 * DBに登録
 */

// filterマッチしなかったものを先にupdate
foreach($cursorlist as $rid => $cursor){
	if(!isset($alerted_pulls[$rid])){
		RepositoryDb::updateCursor($rid,$cursor);
	}
}

// アラートがあるものはrepo毎のトランザクションで一括保存
$con = mfwDBConnection::getPDO('default_master');
$repoids = array_keys($alerted_pulls);
foreach($repoids as $rid){
	echo "save: {$repos[$rid]->getName()} ... ";
	$con->beginTransaction();
	try{
		foreach($alerts[$rid] as $alert){
			$pra = new PullRequestAlert($alert);
			$pra->save($con);
		}
		foreach($alerted_pulls[$rid] as $p){
			$pr = new PullRequest(array(
				'repo_id' => $rid,
				'number' => $p['number'],
				'user' => $p['user']['login'],
				'title' => $p['title'],
				'pull_created_at' => $p['created_at'],
				));
			$pr->save($con);
		}
		RepositoryDb::updateCursor($rid,$cursorlist[$rid],$con);
		$con->commit();
	}
	catch(Exception $e){
		$con->rollback();
		throw $e;
	}
	echo "done.\n";
}

echo "complete.\n";
