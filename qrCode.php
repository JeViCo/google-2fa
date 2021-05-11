<?php

class QrCode
{
	private $qrImage;

	private function addLogo( $logoSrc, $contextLen )
	{
		$qrSize = imagesy( $this->qrImage );
		$backSize = $qrSize / 3;
		$borderSize = ( $backSize / 12 ); // Отступ
		$logoSize = $backSize - $borderSize * 2;

		// Обрабатываем логотип
		$logo = file_get_contents( $logoSrc );
		if ($logo === false)
			return FALSE;
		$logo = imagecreatefromstring( $logo );

		// Добавляем границы
		
		$background = imagecreatetruecolor( $backSize, $backSize );
		$white = imagecolorallocate( $background, 255, 255, 255);
		imagefilledrectangle( $background, 0, 0, $backSize, $backSize, $white );

		imagecopyresampled( $background, $logo, $borderSize, $borderSize, 0, 0, $logoSize, $logoSize, imagesx( $logo ), imagesy( $logo ) );
		imagedestroy( $logo );
		
		// Конечное лого добавляем в QR-код
		imagecopyresampled( $this->qrImage, $background, $backSize, $backSize, 0, 0, $backSize, $backSize, $backSize, $backSize );
	}

	public function createQrCode ( $content, $logo, $size = '256x256' )
	{

		$this->qrImage = imagecreatefrompng( 'https://chart.googleapis.com/chart?cht=qr&chld=H|1&chs=' . $size . '&chl=' . urlencode( $content ) );

		if ( !empty( $logo ) )
		{
			$this->addLogo( $logo, mb_strlen( $content ) );
		}

		//header('Content-type: image/png');
		imagepng( $this->qrImage );
		imagedestroy( $this->qrImage );
		unset( $this->qrImage );
	}
}

$content = is_null( $_GET['content'] ) ? 'https://youtu.be/dQw4w9WgXcQ' : $_GET['content'];
$size = is_null( $_GET['size'] ) ? '256x256' : $_GET['size'];
$logo = is_null( $_GET['logo'] ) ? 'https://styles.redditmedia.com/t5_2qhk5/styles/communityIcon_v58lvj23zo551.jpg' : $_GET['logo'];

$QR = new QrCode();
$QR->createQrCode( $content, $logo, $size );