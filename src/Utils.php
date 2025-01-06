<?php

namespace Feelri\Utils;

use \Closure;
use Exception;
use Feelri\Utils\Traits\StaticTrait;

class Utils
{
	use StaticTrait;

	/**
	 * 字节转可读size
	 * @param string $bytes
	 * @return string
	 */
	public function bytesToSize(string $bytes): string
	{
		$sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB');
		$i = (int) floor(log($bytes) / log(1024));
		return round($bytes / pow(1024, $i), 2) . $sizes[$i];
	}

	/**
	 * 获取用户首选语言
	 *
	 * 该方法解析 HTTP 头部中的 Accept-Language 字段，以确定用户的首选语言
	 * 如果无法确定，则返回默认语言设置
	 *
	 * @param string $acceptLanguage HTTP 头部中的 Accept-Language 字段值
	 * @param string $default 默认语言设置，当无法确定用户首选语言时使用
	 * @return string 用户的首选语言列表，按用户偏好降序排列
	 */
	public function getPreferredLanguage(string $acceptLanguage, string $default = 'zh-CN'): string
	{
		$languages = [];
		foreach (explode(',', $acceptLanguage) as $lang) {
			[$language, $quality] = array_merge(explode(';q=', $lang), [1]);
			$languages[$language] = (float) $quality;
		}
		arsort($languages);
		$languages = array_keys($languages);
		return $languages[0] ?? $default;
	}

	/**
	 * 抑制异常
	 *
	 * @param Closure      $callback
	 * @param mixed|null   $default
	 * @param Closure|null $errorHandler
	 * @return mixed
	 */
	public function ignoreException(Closure $callback, mixed $default = null, Closure $errorHandler = null): mixed
	{
		try {
			return $callback();
		} catch (\Throwable $e) {
			if ($errorHandler) {
				$errorHandler($e);
			}
		}
		return $default;
	}

	/**
	 * 重试
	 *
	 * @param Closure $callback
	 * @param int     $times
	 * @param int     $sleep
	 * @return mixed
	 * @throws Exception
	 */
	public function retry(Closure $callback, int $times, int $sleep = 0): mixed
	{
		$error = null;
		for ($attempt = 1; $attempt <= $times; $attempt++) {
			try {
				return $callback($attempt);
			} catch (Exception $e) {
				$error = $e;
				if ($attempt < $times) {
					if ($sleep > 0) {
						usleep($sleep * 1000); // 转换为微秒
					}
					continue;
				}
			}
		}
		throw $error;
	}

	/**
	 * 字符串遮罩
	 *
	 * @param string $string
	 * @param int    $length
	 * @param int    $start
	 * @return string
	 */
	public function mask(string $string, int $length = 0, int $start = 0): string
	{
		if (empty($string)) {
			return '';
		}

		$strLength = mb_strlen($string);

		if ($length === 0) {
			$length = (int)ceil($strLength / 3);
			$start = (int)floor(($strLength - $length) / 2);
		}

		if ($start >= $strLength) {
			return $string;
		}

		if (($start + $length) > $strLength) {
			$length = $strLength - $start;
		}

		return mb_substr($string, 0, $start)
			. str_repeat('*', $length)
			. mb_substr($string, $start + $length);
	}

	/**
	 * 检测字符串是否为链接
	 *
	 * @param string $str
	 * @return bool
	 */
	public function isLink(string $str): bool
	{
		$preg = "/(https?|ftp|file):\/\/[-A-Za-z0-9+&@#\/\%?=~_|!:,.;]+[-A-Za-z0-9+&@#\/\%=~_|]/";
		preg_match_all($preg, $str ,$arr);
		return !empty($arr[0]);
	}

	/**
	 * url 参数转数组
	 * @param string $url
	 * @return array
	 */
	public function urlQueryToArray(string $url): array
	{
		$data = explode('&', parse_url($url, PHP_URL_QUERY));
		$arr = [];
		foreach ($data as $item) {
			$itemData = explode('=', $item);
			$arr[$itemData[0]] = $itemData[1];
		}

		return $arr;
	}
}
