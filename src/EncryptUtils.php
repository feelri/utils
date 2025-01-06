<?php

namespace Feelri\Utils;

use Exception;

/**
 * openssl
 */
class EncryptUtils
{
	/**
	 * 密钥
	 * @var string
	 */
	private string $pass = '2der4yJc7pJySNE3bHIbg3M7snolteoz'; // Str::random(32)

	/**
	 * iv
	 * @var string
	 */
	private string $iv = 'niItD46FbJ0kdPxg'; // Str::random(16)

	/**
	 * 设置密钥
	 * @param string $pass
	 * @return $this
	 */
	public function setPass(string $pass): static
	{
		$this->pass = $pass;
		return $this;
	}

	/**
	 * 设置iv
	 * @param string $iv
	 * @return $this
	 */
	public function setIv(string $iv): static
	{
		$this->iv = $iv;
		return $this;
	}

	/**
	 * 加密
	 *
	 * @param mixed $value
	 * @param int $expires -1：永久有效
	 * @param string $cipher_algo
	 * @param int $options
	 * @return bool|string
	 */
	public function encrypt(
		mixed $value,
		int $expires = -1,
		string $cipher_algo = 'AES-256-CBC',
		int $options = 0
	): bool|string
	{
		$value = json_encode([
			$value,
			time(),
			$expires
		]);
		return openssl_encrypt($value, $cipher_algo, $this->pass, $options, $this->iv);
	}

	/**
	 * 解密
	 *
	 * @param string $raw
	 * @param string $cipher_algo
	 * @param int    $options
	 * @return mixed
	 * @throws Exception
	 */
	public function decrypt(
		string $raw,
		string $cipher_algo = 'AES-256-CBC',
		int $options = 0
	): mixed
	{
		$json = openssl_decrypt($raw, $cipher_algo, $this->pass, $options, $this->iv);
		[$value, $startTime, $expires] = json_decode($json, true);

		if ($expires > 0 && time() > $startTime + $expires) {
			throw new Exception("校验失败");
		}

		return $value;
	}
}