<?php

namespace Woojin\GoodsBundle\Importer\Setter;

class MappingSetter
{
    /**
     * 設置方法對應陣列
     * 
     * @param [array] $mapping   
     * @param [object] $cells     
     * @param [object] $accessor  
     * @param [array] $translates
     */
    public function setMapping(&$mapping, $cells, $accessor, $translates)
    {
        return $this
            ->initMapping($cells, $mapping, $accessor, $translates)          
            ->fillMappings($accessor, $mapping)
        ;
    }

    /**
     * 初始化 $mapping 陣列
     * 
     * @param  [object] $cells     
     * @param  [array] $mapping   
     * @param  [object] $accessor  
     * @param  [array] $translates          
     */
    protected function initMapping($cells, &$mapping, $accessor, $translates)
    {
        // $cellNum 是從0開始，別和 $rowNum 搞混了
        foreach ($cells as $cellNum => $cell) {
            // 如果沒有對應的動作，則直接進行下一次迭代
            if (!$tmpAct = array_search(trim($cell), $translates)) {
                continue;
            }

            // 根據欄位內容組成 mapping
            $accessor->setValue($mapping, '[' . $cellNum . ']', $tmpAct);                         
        }

        return $this;
    }

    /**
     * 補足 $mapping 必需要有的元素
     * 
     * @param  [type] $accessor
     * @param  [type] $mapping 
     */
    protected function fillMappings($accessor, &$mapping)
    {
        return $this
            // 檢查mapping 有無 setStatus 方法, 若為沒有則添加
            ->fillAssignMapping($accessor, $mapping, 'setStatus')

            // 檢查mapping 有無 setAllowDiscount 方法, 若為沒有則添加
            ->fillAssignMapping($accessor, $mapping, 'setAllowDiscount')
            
            // 檢查mapping 有無 setIsWeb 方法, 若為沒有則添加
            ->fillAssignMapping($accessor, $mapping, 'setIsWeb')

            // 檢查mapping 有無 setStore 方法, 若為沒有則添加
            ->fillAssignMapping($accessor, $mapping, 'setStore')
        ;
    }

    /**
     * 補足指定的 $mapping 元素
     * 
     * @param  [object] $accessor  
     * @param  [array] $mapping   
     * @param  [string] $methodName
     */
    protected function fillAssignMapping($accessor, &$mapping, $methodName)
    {
        if (!in_array($methodName, $mapping)) {
            $accessor->setValue($mapping, '[' . count($mapping) . ']', $methodName);
        }

        return $this;
    }
}