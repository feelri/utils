<?php

namespace Feelri\Utils\Enums;

/**
 * HTTP 状态码
 */
enum HTTPStatusEnum: int
{
	use CollectTrait;

	/**
	 * 请求成功
	 */
	case Ok       = 200; // 成功
	case Created  = 201; // 创建了新的资源
	case Accepted = 202; // 尚未进行处理

	/**
	 * 客户端错误
	 */
	case Bad              = 400;
	case Unauthorized     = 401; // 未授权
	case Payment          = 402; // 未付款
	case Forbidden        = 403; // 拒绝授权访问
	case NotFound         = 404; // 资源不存在
	case MethodNotAllowed = 405; // 请求方式不允许
	case ParamBad         = 422; // 参数错误

	/**
	 * 服务端错误
	 */
	case Error       = 500; // 服务端错误
	case Unavailable = 503; // 服务不可用

	/**
	 * 枚举文本转换
	 * @return string
	 */
	public function label(): string
	{
		return match ($this) {
			self::Ok               => __('messages.http_status.ok'),
			self::Created          => __('messages.http_status.created'),
			self::Accepted         => __('messages.http_status.accepted'),
			self::Bad              => __('messages.http_status.bad'),
			self::Unauthorized     => __('messages.http_status.unauthorized'),
			self::Payment          => __('messages.http_status.payment'),
			self::Forbidden        => __('messages.http_status.forbidden'),
			self::ParamBad         => __('messages.http_status.paramBad'),
			self::Error            => __('messages.http_status.error'),
			self::NotFound         => __('messages.http_status.not_found'),
			self::MethodNotAllowed => __('messages.http_status.method_not_allowed'),
			self::Unavailable      => __('messages.http_status.unavailable'),
		};
	}
}
