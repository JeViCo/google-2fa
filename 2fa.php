<?php

/* Статьи
	https://medium.com/@tilaklodha/google-authenticator-and-how-it-works-2933a4ece8c2
	https://ru.wikipedia.org/wiki/Google_Authenticator
	https://www.rfc-editor.org/info/rfc6238
*/

/* Библиотеки
	https://github.com/ChristianRiesen/base32
*/

include 'Base32.php';

class twoFactor
{
	private const CODE_LENGTH = 6;
	private const TIME_DISPERSION = 1;

	public function generatePassword( $secretLength = 16 )
	{
		$validChars = Base32::getBase32LookupTable( );
		array_pop( $validChars ); // Без знака 'равно'
		$validChars = implode( '', $validChars );

		if ($secretLength < 16 || $secretLength > 128) {
			throw new Exception('Неверная длина секрета!');
		}

		$secret = '';
		$length = strlen( $validChars );
		for ($i = 0; $i < $secretLength; $i++) {
			$secret .= $validChars[ rand(0, $length - 1) ];
		}

		return $secret;
	}

	private function getCode( $disp = 0, $secret )
	{
		$time = floor(time( ) / 30) + $disp; // Смещение во времени (часы спешат/задержки и прочее)
		$time = chr(0) .chr(0) .chr(0) .chr(0) .pack('N*', $time); // Преобразуем строку в байты и дополняем нулями до длины ключа
		$secretKey = Base32::decode( $secret ); // Также преобразуем строку в байты

		$hash = hash_hmac( 'SHA1', $time, $secretKey, true ); // Ставим true для работы с двоичкой

		$offset = ord( substr($hash, -1) ) & 0x0F; // Последний полубайт (Получаем последний байт, используем логическое И для получения полубайта)
		$hashpart = substr( $hash, $offset, 4 ); // Получаем 4 байта из хеша

		$value = unpack( 'N', $hashpart )[ 1 ]; // Распаковываем, так как больше не работаем с двоичкой. Результат всегда под 1 индексом
		$value = $value & 0x7FFFFFFF; // Убираем старший БИТ!! Не байт!! (делаем число в пределах 32 бит)

		$code = $value % pow( 10, static::CODE_LENGTH ); // Находим остаток от деления на 10^длина_кода
		$code = str_pad( $code, static::CODE_LENGTH, '0', STR_PAD_LEFT ); // Дополняем нулями в начале до длины кода

		//echo $code . '<br>'; // Убрать!!!
		return $code;
	}

	public function verifyCode( $code, $secret )
	{
		for ($i = -static::TIME_DISPERSION; $i <= static::TIME_DISPERSION; $i++)
		{
			if ( $this->getCode( $i, $secret ) === $code )
			{
				return TRUE;
			};
		}
		return FALSE;
	}

	public function getLinkBySecret( $secret, $name = '2FA TEST' ) {
		return urlencode( 'otpauth://totp/' . $name . '?secret=' . $secret );
	}
}