<?php

namespace Feelri\Utils\Enums;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait CollectTrait
{
    /**
     * cases 集合列表
     * @return Collection
     */
    public static function collect(): Collection
    {
		$array = [];
		foreach (self::cases() as $case) {
			$item = (array) $case;
			$item['label'] = $case->label();
			$array[] = $item;
		}
        return Collection::make($array);
    }

    /**
     * 获取 name 数组
     * @return array
     */
    public static function names(): array
    {
        return self::collect()->pluck('value')->toArray();
    }

    /**
     * 获取 value 数组
     * @return array
     */
    public static function values(): array
    {
        return self::collect()->pluck('value')->toArray();
    }

    /**
     * 获取 labels 数组
     * @return array
     */
    public static function labels(): array
    {
        return self::collect()->pluck('label')->toArray();
    }

	/**
	 * 获取枚举 maps
	 *
	 * @param bool   $isKeySnake
	 * @param string $nameLabel
	 * @param string $valueLabel
	 * @return array
	 */
    public static function maps(bool $isKeySnake = true, string $nameLabel = 'name', string $valueLabel = 'value'): array
    {
        $cases = self::collect();
        $keys = $cases->pluck($cases, $nameLabel)->toArray();
        $values = $cases->pluck($cases, $valueLabel)->toArray();

        if ($isKeySnake) {
            foreach ($keys as &$key) {
                $key =  Str::snake($key);
            }
        }

        return array_combine($keys, $values);
    }

    /**
     * 获取 key 名
     * @param bool $isKeySnake
     * @return string
     */
    public function getName(bool $isKeySnake = true): string
    {
        return $isKeySnake ? Str::snake($this->name) : Str::camel($this->name);
    }

	/**
	 * implode 枚举类
	 *
	 * @param string $separator
	 * @return string
	 */
	public static function implode(string $separator = ","): string
	{
		return implode($separator, array_column(self::cases(), 'value'));
	}

	/**
	 * 枚举 label
	 * @return mixed
	 */
	public function label(): mixed
	{
		return $this->name;
	}
}
