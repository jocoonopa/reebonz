<?php

namespace Woojin\Entity;

class ProGetter
{
	public function getDate($instance, $format = 'Y-m-d')
	{
		return (!is_object($instance)) ? '' : $instance->format($format);
	}

	public function getName($instance, $default = '')
	{
		return (!is_object($instance)) ? $default : $instance->getName();
	}

	public function getTrueFalseDes($val)
	{
		return ($val == 1 ? '是' : '否');
	}
}

