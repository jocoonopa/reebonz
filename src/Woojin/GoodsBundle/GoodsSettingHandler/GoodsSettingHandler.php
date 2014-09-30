<?php 

namespace Woojin\GoodsBundle\GoodsSettingHandler;

/**
 * 處理傳入工廠的 $settings 陣列
 */
class GoodsSettingHandler
{
    /**
     * 設定陣列
     * 
     * @var array
     */
	protected $settings = array();

    /**
     * 關連實體處理者
     * 
     * @var [\Woojin\GoodsBundle\GoodsSettingHandler\RelatedEntityHandler]
     */
    protected $relatedEntityHandler;

    /**
     * 非關連實體處理者
     * 
     * @var [\Woojin\GoodsBundle\GoodsSettingHandler\NotRelatedEntityHandler]
     */
    protected $notRelatedEntityHandler;

    public function __construct(\Woojin\GoodsBundle\GoodsSettingHandler\RelatedEntityHandler $relatedEntityHandler, \Woojin\GoodsBundle\GoodsSettingHandler\NotRelatedEntityHandler $notRelatedEntityHandler)
    {
        $this->relatedEntityHandler = $relatedEntityHandler;

        $this->notRelatedEntityHandler = $notRelatedEntityHandler;
    }

	/**
	 * 取得設定陣列
	 * 
	 * @return [array]
	 */
	public function get()
	{
		return $this->settings;
	}

	/**
     * 設置更新商品設定陣列
     * 
     * @param [object] $accessor
     * @param [array] $settings
     * @param [object] $request
     */
    public function run($request, $accessor, $em)
    {      
        $this->notRelatedEntityHandler->run($request, $accessor, $this->settings);
        
        $this->relatedEntityHandler->run($request, $accessor, $em, $this->settings);

        return $this;
    }
}