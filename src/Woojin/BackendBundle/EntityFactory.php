<?php

namespace Woojin\BackendBundle;

interface EntityFactory
{
	/**
	 * 根據傳入的組態創造實體
	 * 
	 * @param  [array] $settings
	 * @return [object]
	 */
	public function create($settings);

	/**
	 * 將指定的實體更新為傳入的組態直
	 * 
	 * @param  [array] $settings
	 * @param  [object] $entity
	 * @return [object]    
	 */
	public function update($settings, $entity);

	/**
	 * 複製傳入的實體
	 * 
	 * @param  [object] $entity
	 * @param  [integer] $amount
	 * @return [array(object)]    
	 */
	public function copy($entity, $amount);
}