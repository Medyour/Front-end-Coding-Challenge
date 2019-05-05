<?php
session_start();

//pagination
if(isset($_POST['pagination']))
{
	if($_POST['pagination']=='Next')
	{
		$_SESSION['page']++;
		if($_SESSION['page']==34)
		{
			$btn_next='id="btn_next" disabled=""';
		}
		else
		{
			$btn_next="";
		}
	}
	if($_POST['pagination']=='Previous')
	{
		$_SESSION['page']--;
		if($_SESSION['page']==1)
		{
			$btn_previous='id="btn_prev" disabled="" ';
		}
		else
		{
			$btn_previous="";
		}
	}
}
else
{
	$_SESSION['page']=1;
	$btn_previous='id="btn_prev" disabled=""';
}
	
//Creates a stream context
$context = stream_context_create(
    array(
        "http" => array(
            "header" => "User-Agent: Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/50.0.2661.102 Safari/537.36"
        )
    )
);

//Create inerval date of 30 day Ago
$date=date_create(date("Y-m-d"));
date_add($date,date_interval_create_from_date_string("-30 days"));
$interval_date= date_format($date,"Y-m-d");
// URL API
$url_gethub=file_get_contents('https://api.github.com/search/repositories?q=created:>'.$interval_date.'&sort=stars&order=desc&page='.$_SESSION['page'], false, $context);

$datagethub = json_decode($url_gethub,true);

?>

<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Front-end Coding Challenge</title>
		<meta property="og:image" content="https://unitedremote.com/images/og-image.png">
		<link rel="stylesheet" href="css/style.css" type="text/css"  />
	</head>
	<body>
		<div id="content">
			<div id="block_header">
				<a id="logo" href="https://unitedremote.com"><img src="img/logo.png" alt="United Remote" title="United Remote" /></a>
			</div>
			<div id="liste">
				<h1>List of the most starred Github repos created in the last 30 days</h1>
				<?php
					foreach($datagethub['items'] as $items)
					{
						//Calculate number of day submitted
						$date_create_at = substr($items['created_at'],0,10);
						$date_create_at  = strtotime($date_create_at);
						$newformat = date('Y-m-d', $date_create_at );
						$now = time();
						$date = strtotime($newformat);
						$datediff = $now - $date;
						$nbr_Day =  round($datediff / (60 * 60 * 24));
						//Show Repo Info
						echo'
						<div class="repository">
							<div class="avatar">
								<img src="'.$items['owner']['avatar_url'].'" width="100px" height="100px" />
							</div>
							<div class="info">
								<h3 class="name">'.$items['name'].'</h3>
								<p class="description">'.$items['description'].'</p>
								<span class="nbr_stars">Starts: '.$items['stargazers_count'].'</span>
								<span class="nbr_issues">Issues: '.$items['open_issues'].'</span>
								<span class="interval">Submitted '.$nbr_Day.' days ago by '.$items['name'].'</span>
							</div>
						</div>';
					}
				?>
			</div>
			<div id="pagination">
				<form action="index.php" method="post" >
				<input type="submit" name="pagination" value="Previous" class="btn" <?php echo $btn_previous;?> />
				<input type="submit" name="pagination" value="Next" class="btn next"<?php echo $btn_next;?> />
				</form>
			</div>
		</div>
	</body>
</html>