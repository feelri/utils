<?php

namespace Feelri\Utils\Traits;

/**
 * 静态入口方法
 */
trait StaticTrait
{
	/**
	 * 入口方法
	 * @return static
	 */
	public static function static(): static
	{
		return new static();
	}
}