<?php

namespace Woojin\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class WoojinUserBundle extends Bundle
{
	/**
	 * 取得最後登入記錄實體
	 * 
	 * @param  [array] $userLogs [登入記錄實體陣列，由新到舊排序]
	 * @return [object] [最後登入記錄實體]
	 */
	public function getLastLoginLog($userLogs)
	{
		/**
		 * 最後登入記錄位置
		 * @var integer
		 */
		$position = (is_null($userLogs) || count($userLogs) < 2) ? 0 : 1;

		return $userLogs[$position];
	}
}
