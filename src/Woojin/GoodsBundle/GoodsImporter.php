<?php

namespace Woojin\GoodsBundle;

use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class GoodsImporter
{
    const NONE_ENTITY       = 0;
    const NO_IMG            = '/img/404.png';
    const ROW_START         = 1;
    const IS_ALLOW          = 1;
    const IS_NORMAL         = 0;
    const IS_CONSIGN        = 1;
    
    const GS_ON_SALE        = 1;
    const GS_OFF_SALE       = 4;
    const OK_IN             = 1;
    const OK_EXCHANGE_IN    = 2;
    const OK_TURN_IN        = 3;
    const OK_MOVE_IN        = 4;
    const OK_CONSIGN_IN     = 5;
    const OK_OUT            = 6;
    const OK_EXCHANGE_OUT   = 7;
    const OK_TURN_OUT       = 8;
    const OK_MOVE_OUT       = 9;
    const OK_FEEDBACK       = 10;
    const OK_WEB_OUT        = 11;
    const OK_SPECIAL_SELL   = 12;
    const OK_SAME_BS        = 13;
    const OS_HANDLING       = 1;
    const OS_COMPLETE       = 2;
    const OS_CANCEL         = 3;

    protected $container;
    protected $context;
    protected $registry;

    public function __construct(ManagerRegistry $registry, ContainerInterface $container, SecurityContext $context)
    {
        $this->container = $container;
        $this->context = $context;
        $this->registry = $registry;
    }

    protected function getImportRows($request) 
    {
        /**
         * 檔案絕對路徑
         * 
         * @var [string]
         */
        $path = $this->importFileMove($request);

        /**
         * 讀取前面上傳的excel檔案，並轉換成物件
         * 
         * @var object
         */
        $excelObj = \PHPExcel_IOFactory::load($path);

        // 檔案讀取完立刻刪除
        @unlink($path);

        /**
         * Worksheet
         * 
         * @var object
         */
        $workSheet = $excelObj->getActiveSheet();

        return $workSheet->getRowIterator();
    }

    /**
     * 取得首欄對應陣列
     * 
     * @return [array]
     */
    protected function getTranslateArray()
    {
        return array(
            'setStore' => '部門*',
            'setPurchaseAt' => '進貨日*', 
            'setExpirateAt' => '到期日',
            'setSupplier' => '廠商*', 
            'setBrand' => '品牌*', 
            'setOrgSn' => 'SKU',
            'setName' => '商品描述*', 
            'setPattern' => '款式*', 
            'setMt' => '材質*',
            'setColor' => '花色*',
            'setSource' => '來源*',
            'setLevel' => '商品狀況', 
            'setDpo' => 'DPO#', 
            'setCost' => '單價成本*(含稅)',
            'amount' => '數量*', 
            'setFakePrice' => '市價', 
            'setPrice' => '優惠價*',
            'setMemo' => '備註', 
            'setAllowDiscount' => '允許折扣', 
            'setIsWeb' => '允許網路販售',
            'setImgpath' => '對應圖片',
            'setConsigner' => '客戶信箱',
            'setFeedBack' => '回扣'
        );
    }

    /**
     * 組成設定參數陣列
     *
     * @param   [object] $accessor
     * @param   [array] $methodMapping
     * @param   [string] $act 
     * @param   [string] $arg
     * @return  [string|integer|object]
     */
    public function getSettingsVal($accessor, $methodMapping, $act, $arg)
    {   
        /**
         * 回傳值
         * 
         * @var [string|integer|object]
         */
        $return = null;

        $this->switchFlowWithAct($return, $act, $methodMapping, $accessor, $arg);

        return $return;
    }

    /**
     * [switchFlowWithAct]
     * 
     * @param  [null] $return   
     * @param  [string] $act      
     * @param  [array] $mapping  
     * @param  [object] $accessor 
     * @param  [object] $arg      
     */
    protected function switchFlowWithAct(&$return, $act, $mapping, $accessor, $arg)
    {
        switch ($act)
        {
            case 'setPurchaseAt':
            case 'setExpirateAt':

                $return = new \DateTime(date('Y-m-d', strtotime($arg)));

                break;

            case 'setSupplier':
            case 'setBrand':
            case 'setPattern':
            case 'setColor':
            case 'setLevel':
            case 'setStatus':
            case 'setMT':
            case 'setSource':
            case 'setConsigner':
            case 'setStore':

                $return = $this->getReturnEntity($accessor, $mapping, $act, $arg);
                
                if (!$this->isValidReturnEntity($return, $arg)) {
                    $msg = $this->errorMsgOfImport($accessor, $act, $arg);

                    exit(json_encode($msg));
                };

                break;

            case 'setOrgSn':    
            case 'setName':
            case 'setDpo':
            case 'setCost':
            case 'amount':
            case 'setFakePrice':
            case 'setPrice':
            case 'setFeedBack':
            case 'setBrandSn':            
            case 'setDes':
            case 'setMemo':
            case 'setImgpath':

                $return = strval($arg);

                break;

            case 'setIsWeb':
            case 'setAllowDiscount':
                // 若為空是1(預設值), 否則自己
                $tmp = ((string) $arg === '') ? 1 : strval($arg); 
                
                // 轉數字->字串
                $return = ((int) strval($tmp) === 1) ? '1' : '0';

                break;

            default:
                break;
        }
    }

    /**
     * 取得實體
     * 
     * @param  [object] $accessor
     * @param  [array] $mapping 
     * @param  [string] $act     
     * @param  [object] $arg     
     * @return [object]          
     */
    protected function getReturnEntity($accessor, $mapping, $act, $arg) 
    {
        $attr = $accessor->getValue($mapping, '[' . $act . '][attr]');

        /**
         * 條件陣列
         * 
         * @var array
         */
        $condition = array();

        $accessor->setValue($condition, '[' . $attr . ']', $arg);

        $em = $this->registry->getManager();

        return $em->getRepository($accessor->getValue($mapping, '[' . $act . '][bundle]'))->findOneBy($condition);
    }

    /**
     * 判斷實體是否合法
     * 
     * @param  [object]  $return
     * @param  [object]  $arg   
     * @return boolean        
     */
    protected function isValidReturnEntity($return, $arg)
    {
        return (is_object($return) || !empty($return) || strval($arg) == '' );
    }

    /**
     * 錯誤訊息匯入
     * 
     * @param  [object] $accessor
     * @param  [string] $act     
     * @param  [object] $arg     
     * @return [array] $feedback     
     */
    protected function errorMsgOfImport($accessor, $act, $arg)
    {
        /**
         * act動作和對應的實體
         * 
         * @var array
         */
        $mapping = $this->getMappingArray();

        /**
         * 回饋的錯誤訊息
         * 
         * @var array
         */
        $feedback = array();

        $notYetMsg = $accessor->getValue($mapping, '[' . $act . '][name]');

        $accessor->setValue($feedback, '[error]',  $notYetMsg . $arg . '尚未建立!');

        $accessor->setValue($feedback, '[name]', strval($arg));

        if ($entity = $accessor->getValue($mapping, '[' . $act . '][entity]')) {
            $accessor->setValue($feedback, '[resource]', $entity);
        }

        return $feedback;
    }

    /**
     * get Mapping array
     * 
     * @return [array]
     */
    protected function getMappingArray()
    {
        return array(
            'setSupplier' => array(
                'bundle' => 'WoojinGoodsBundle:Supplier',
                'name' => '廠商', 
                'entity' => 'supplier',
                'attr' => 'name'
            ),
            'setBrand' => array(
                'bundle' => 'WoojinGoodsBundle:Brand',
                'name' => '品牌', 
                'entity' => 'brand',
                'attr' => 'name'
            ),
            'setPattern' => array(
                'bundle' => 'WoojinGoodsBundle:Pattern',
                'name' => '款式', 
                'entity' => 'pattern',
                'attr' => 'name'
            ),
            'setColor' => array(
                'bundle' => 'WoojinGoodsBundle:Color',
                'name' => '顏色', 
                'entity' => 'color',
                'attr' => 'name'
            ),
            'setLevel' => array(
                'bundle' => 'WoojinGoodsBundle:GoodsLevel',
                'name' => '商品狀況', 
                'entity' => 'level',
                'attr' => 'name'
            ),
            'setStatus' => array(
                'bundle' => 'WoojinGoodsBundle:GoodsStatus',
                'name' => '狀態', 
                'entity' => 'status',
                'attr' => 'id'
            ),
            'setMT' => array(
                'bundle' => 'WoojinGoodsBundle:GoodsMT',
                'name' => '材質', 
                'entity' => 'goodsMt',
                'attr' => 'name'
            ),
            'setSource' => array(
                'bundle' => 'WoojinGoodsBundle:GoodsSource',
                'name' => '來源', 
                'entity' => 'goodsSource',
                'attr' => 'name'
            ),
            'setConsigner' => array(
                'bundle' => 'WoojinOrderBundle:Custom',
                'name' => '客戶',
                'attr' => 'email'
            ),
            'setStore' => array(
                'bundle' => 'WoojinStoreBundle:Store',
                'name' => '部門',
                'attr' => 'sn'
            )
        );
    }

    /**
     * 檢查email 是否有效
     * 
     * @param  string  $email
     * @return boolean   
     */
    protected function isValidEmail($email)
    {
        // 過濾空白字串防止有人手殘
        $email = trim($email);

        if (in_array($this->emails, $email)) {
            return true;
        }

        /**
         * 客戶實體
         * 
         * @var \Woojin\OrderBundle\Entity\Custom
         */
        $custom = $this->getDoctrine()->getRepository('WoojinOrderBundle:Custom')->findOneBy(array('email' => $email));

        if ($custom) {
            array_push($this->emails, $email);

            return true;
        }

        return false;
    }

    /**
     * 移動上傳檔案至指定位置
     * 
     * @param  [object] $request
     * @return [string]
     */
    protected function importFileMove($request)
    {
        /**
         * 取得檔案
         * 
         * @var object
         */
        $files = $request->files->get('file');

        // 無上傳檔案則不動作
        if (!$files->isValid()) {
            throw new \Exception('No File has been loaded!');
        }

        /**
         * 取得目前使用者
         * 
         * @var object
         */
        $user = $this->context->getToken()->getUser();

        /**
         * 資料夾絕對路徑
         * 
         * @var string
         */
        $filepath = $request->server->get('DOCUMENT_ROOT') . '/uploads/'. $user->getId();

        /**
         * 檔案名稱
         * 
         * @var string
         */
        $fileName = md5('import_file' . date('H:i:s')) . 'xlsx';

        // 移動檔案
        $files->move($filepath, $fileName);

        return $filepath . '/' . $fileName;
    }

    /**
     * 開始迭代excel 進行新增商品動作
     * 
     * @param  [object] $em            
     * @param  [object] $accessor  
     * @param  [array] $translates
     */
    public function iterateRowToCreate(&$em, &$goodsCollection, $request)
    {
    	/**
         * 取得所有的 row 
         * 
         * @var array
         */
    	$rows = $this->getImportRows($request);

        /**
         * 使用者
         * 
         * @var Woojin\UserBundle\Entity\User
         */
        $user = $this->context->getToken()->getUser();

        /**
         * 這個工廠將會替我們創建新的商品實體
         * 
         * @var object
         */
        $GoodsFactory = $this->container->get('goods.factory');

        /**
         * Symfony 的屬性套件，透過它可以用物件方式讀寫陣列
         * 
         * @var object
         */
        $accessor = PropertyAccess::createPropertyAccessor();

        /**
         * 中文欄位名稱轉換為英文陣列鍵名之對應陣列
         * 
         * @var array
         */
        $translates = $this->getTranslateArray();

        /**
         * 實體，名稱以及 Angular 屬性的陣列
         * 
         * @var array
         */
        $methodMapping = $this->getMappingArray();

        /**
         * 欄位順位-資料屬性對應陣列，
         * 會在迭代 $rows 的第一行時組成
         * 
         * @var array
         */
        $mapping = array();

        foreach ($rows as $rowNum => $row) {
            /**
             * 提供給工廠的參數陣列，在此Action裡該參數會隨著每行迭代重新刷新
             * 
             * @var array
             */
            $settings = array();

            // 取得本列的所有格子
            $cells = $row->getCellIterator();

            // true: 空的格子一樣要迭代
            $cells->setIterateOnlyExistingCells(false);

            // 第一行是欄位名稱，用來形成 key-attribute 的 mapping
            if ($rowNum === self::ROW_START) {

                $this->setMapping($mapping, $cells, $accessor, $translates);

                continue;
            }

            $this
                ->iterateCellsToSetSettings($cells, $mapping, $methodMapping, $settings, $accessor, $request)
                ->setNullAttr($accessor, $settings, $methodMapping, $user)
            ;

            // 迭代加入 goodsCollection
            $createdGoods = $GoodsFactory->lazyCreate($settings, $em);
            array_walk($createdGoods, function ($goods) use (&$goodsCollection) {
                array_push($goodsCollection, $goods);
            });

            // 清空設定參數陣列
            $settings = array();
        }

        return $this;
    }

    /**
     * 迭代格子設定 $settings
     * 
     * @param  [object] $cells   
     * @param  [array] $mapping 
     * @param  [array] $methodMapping
     * @param  [array] $settings
     * @param  [object] $accessor
     * @param  [object] $request 
     */
    protected function iterateCellsToSetSettings($cells, $mapping, $methodMapping, &$settings, $accessor, $request)
    {
        // 迭代並且進行新增資料實體的動作
        foreach ($cells as $cellNum => $cell) {
            // 如果是客戶信箱，要在 request 添加 email，這在 order 產生時會用到，
            // 又因為有客戶信箱表示為寄賣商品，所以要把 settings 的 setInType 設置為 1
            if ($method = $accessor->getValue($mapping, '[' . $cellNum . ']')) {
                // 設置參數陣列
                $accessor->setValue($settings, '[' . $method . ']', $this->getSettingsVal($accessor, $methodMapping, $method, $cell));

                if ($method === 'email') {
                    // request 參數 email 設置，訂單會用到此參數
                    $request->request->set('email', $cell);

                    // 進貨類型設置為寄賣
                    $accessor->setValue($settings, '[setInType]', self::IS_CONSIGN);
                } else {
                    // 設置參數陣列
                    $accessor->setValue($settings, '[' . $method . ']', $this->getSettingsVal($accessor, $methodMapping, $method, $cell));
                }
            }
        }

        return $this;
    }

    /**
     * 設置空值屬性
     * 
     * @param [object] $accessor     
     * @param [array] $settings     
     * @param [array] $methodMapping
     * @param [object] $user
     */
    protected function setNullAttr($accessor, &$settings, $methodMapping, $user)
    {
        // 設置商品狀態為上架，強制規定，不開放批次上傳欄位擇定商品狀態，可能會造成系統業務邏輯混亂
        $accessor->setValue($settings, '[setStatus]', $this->getSettingsVal($accessor, $methodMapping, 'setStatus', self::GS_ON_SALE));

        // 如果沒有設置圖片路徑，則給予404png 的路徑，null 不符合原本的邏輯
        if (!$accessor->getValue($settings, '[setImgpath]')) {
            $accessor->setValue($settings, '[setImgpath]', self::NO_IMG);
        }

        // 如果沒有設置允許折扣，預設給1表示允許 (這邊用 === 是因為PHP會把 0 當成 !, 算是語言問題吧...)
        if ($accessor->getValue($settings, '[setAllowDiscount]') === false || $accessor->getValue($settings, '[setAllowDiscount]') == '') {
            $accessor->setValue($settings, '[setAllowDiscount]', self::IS_ALLOW);
        }
        
        // 判斷是否有綁定寄賣客戶，若有為進貨寄賣，否則為一般進貨
        if ($accessor->getValue($settings, '[setConsigner]') === false || $accessor->getValue($settings, '[setConsigner]') == '') {
            $accessor->setValue($settings, '[setInType]', self::IS_NORMAL);
        } else {
            $accessor->setValue($settings, '[setInType]', self::IS_CONSIGN);
        }

        // 如果沒有設置允許折扣，預設給1表示允許
        if ($accessor->getValue($settings, '[setIsWeb]') === false || $accessor->getValue($settings, '[setIsWeb]') == '') {
            $accessor->setValue($settings, '[setIsWeb]', self::IS_ALLOW);
        }

        // 如果沒有設置商店，預設使用目前使用者的所屬店
        if (!$accessor->getValue($settings, '[setStore]')) {
            $accessor->setValue($settings, '[setStore]', $this->getSettingsVal($accessor, $methodMapping, 'setStore', $user->getStore()->getSn()));
        }     

        return $this; 
    }

    /**
     * 設置方法對應陣列
     * 
     * @param [array] $mapping   
     * @param [object] $cells     
     * @param [object] $accessor  
     * @param [array] $translates
     */
    protected function setMapping(&$mapping, $cells, $accessor, $translates)
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

        if (!is_array($mapping)) {
            throw new \Exception('mapping error!');

            break;
        }

        // 檢查mapping 有無 setAllowDiscount 方法, 若為沒有則添加
        if (!in_array('setAllowDiscount', $mapping)) {
            $accessor->setValue($mapping, '[' . count($mapping) . ']', 'setAllowDiscount'); 
        }

        // 檢查mapping 有無 setIsWeb 方法, 若為沒有則添加
        if (!in_array('setIsWeb', $mapping)) {
            $accessor->setValue($mapping, '[' . count($mapping) . ']', 'setIsWeb'); 
        }

        // 檢查mapping 有無 setStore 方法, 若為沒有則添加
        if (!in_array('setStore', $mapping)) {
            $accessor->setValue($mapping, '[' . count($mapping) . ']', 'setStore'); 
        }
    }
}

