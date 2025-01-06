<?php

namespace Feelri\Utils;

use Feelri\Utils\Traits\StaticTrait;

class TreeUtils
{
	use StaticTrait;

	/**
	 * 将扁平数组转换为树形结构
	 *
	 * @param array  $items         原始数组
	 * @param string $idField       ID字段名
	 * @param string $pidField      父ID字段名
	 * @param string $childrenField 子节点字段名
	 * @param mixed  $parentId      根节点的父ID值
	 * @return array
	 */
	public function toTree(
		array $items,
		string $idField = 'id',
		string $pidField = 'parent_id',
		string $childrenField = 'children',
		mixed $parentId = 0
	): array
	{
		$tree = [];

		foreach ($items as $item) {
			if ($item[$pidField] == $parentId) {
				$children = self::toTree(
					$items,
					$idField,
					$pidField,
					$childrenField,
					$item[$idField]
				);

				if ($children) {
					$item[$childrenField] = $children;
				}

				$tree[] = $item;
			}
		}

		return $tree;
	}

	/**
	 * 将树形结构转换为扁平数组
	 *
	 * @param array  $tree          树形数组
	 * @param string $childrenField 子节点字段名
	 * @param int    $level         当前层级
	 * @return array
	 */
	public function toArray(
		array $tree,
		string $childrenField = 'children',
		int $level = 0
	): array
	{
		$result = [];

		foreach ($tree as $item) {
			$children = $item[$childrenField] ?? [];
			unset($item[$childrenField]);

			$item['level'] = $level;
			$result[]      = $item;

			if ($children) {
				$result = array_merge(
					$result,
					self::toArray($children, $childrenField, $level + 1)
				);
			}
		}

		return $result;
	}

	/**
	 * 查找指定节点的所有父节点
	 *
	 * @param array  $items    原始数组
	 * @param mixed  $id       节点ID
	 * @param string $idField  ID字段名
	 * @param string $pidField 父ID字段名
	 * @return array
	 */
	public function findParents(
		array $items,
		mixed $id,
		string $idField = 'id',
		string $pidField = 'parent_id'
	): array
	{
		$parents = [];

		foreach ($items as $item) {
			if ($item[$idField] == $id) {
				$parentId = $item[$pidField];
				if ($parentId) {
					$parents[] = $item;
					$parents   = array_merge(
						$parents,
						self::findParents($items, $parentId, $idField, $pidField)
					);
				}
				break;
			}
		}

		return $parents;
	}

	/**
	 * 查找指定节点的所有子节点
	 *
	 * @param array  $items    原始数组
	 * @param mixed  $id       节点ID
	 * @param string $idField  ID字段名
	 * @param string $pidField 父ID字段名
	 * @return array
	 */
	public function findChildren(
		array $items,
		mixed $id,
		string $idField = 'id',
		string $pidField = 'parent_id'
	): array
	{
		$children = [];

		foreach ($items as $item) {
			if ($item[$pidField] == $id) {
				$children[] = $item;
				$children   = array_merge(
					$children,
					self::findChildren($items, $item[$idField], $idField, $pidField)
				);
			}
		}

		return $children;
	}

	/**
	 * 获取节点的完整路径ID
	 *
	 * @param array  $items       原始数组
	 * @param mixed  $id          当前节点ID
	 * @param string $idField     ID字段名
	 * @param string $pidField    父ID字段名
	 * @param bool   $includeSelf 是否包含当前节点
	 * @return array 从根节点到当前节点的ID路径
	 */
	public function getPathIds(
		array $items,
		mixed $id,
		string $idField = 'id',
		string $pidField = 'parent_id',
		bool $includeSelf = true
	): array
	{
		$path = [];

		// 如果需要包含自身，将当前节点ID添加到路径中
		if ($includeSelf) {
			$path[] = $id;
		}

		foreach ($items as $item) {
			if ($item[$idField] == $id) {
				$parentId = $item[$pidField];
				if ($parentId) {
					// 递归获取父节点路径
					$parentPath = self::getPathIds($items, $parentId, $idField, $pidField, true);
					// 合并路径，父节点在前
					$path = array_merge($parentPath, $path);
				}
				break;
			}
		}

		return $path;
	}

	/**
	 * 获取节点的完整路径数据
	 *
	 * @param array  $items       原始数组
	 * @param mixed  $id          当前节点ID
	 * @param string $idField     ID字段名
	 * @param string $pidField    父ID字段名
	 * @param bool   $includeSelf 是否包含当前节点
	 * @return array 从根节点到当前节点的完整数据路径
	 */
	public function getPathNodes(
		array $items,
		mixed $id,
		string $idField = 'id',
		string $pidField = 'parent_id',
		bool $includeSelf = true
	): array
	{
		$path        = [];
		$currentNode = null;

		// 查找当前节点
		foreach ($items as $item) {
			if ($item[$idField] == $id) {
				$currentNode = $item;
				if ($includeSelf) {
					$path[] = $item;
				}
				break;
			}
		}

		if ($currentNode) {
			$parentId = $currentNode[$pidField];
			while ($parentId) {
				foreach ($items as $item) {
					if ($item[$idField] == $parentId) {
						// 将父节点添加到路径开头
						array_unshift($path, $item);
						$parentId = $item[$pidField];
						break;
					}
				}
			}
		}

		return $path;
	}
}