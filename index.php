<?php

include '2fa.php';

/*	Main section	*/
/*
$QR = new QrCode();
$content = is_null( $_GET['content'] ) ? 'https://youtu.be/dQw4w9WgXcQ' : $_GET['content'];
$size = is_null( $_GET['size'] ) ? '250x250' : $_GET['size'];
$logo = is_null( $_GET['logo'] ) ? 'https://styles.redditmedia.com/t5_2qhk5/styles/communityIcon_v58lvj23zo551.jpg' : $_GET['logo'];
$QR->createQrCode( $content, $size, $logo );
*/

/* 2FA prove stage */
/*
$code = '268042';
$secr = 'DUMMYSECRETDUMMY';

$twoFactor = new twoFactor( );
$result = $twoFactor->verifyCode( $code, $secr );

echo $result ? 'OK<br>' : 'FAILED<br>';
*/

if ( !is_null( $_GET['code'] ) && !is_null( $_GET['secret'] ) )
{
	$twoFactor = new twoFactor( );
	$result = $twoFactor->verifyCode( $_GET['code'], $_GET['secret'] );
	?>

	<div style='display: flex; align-items: center; justify-content: center; flex-direction: column; width: 100%; height: 100%'>
		<div>Result: <? echo $result ? 'SUCCESS<br>' : 'FAILED'; ?></div>
	</div>

	<?
	die( );
}

$twoFactor = new twoFactor( );
$secret = $twoFactor->generatePassword( );
$link = $twoFactor->getLinkBySecret( $secret );
$logo = 'https://www.zimbra.com/wp-content/uploads/2015/11/zimbra-2FA-icon.png';

$image = 'qrCode.php?content=' . $link . '&logo=' . $logo;

?>
<div style='display: flex; align-items: center; justify-content: center; flex-direction: column; width: 100%; height: 100%'>
	<img src = '<?=$image ?>' />
	<div>Secret: <?=$secret ?></div>
</div>