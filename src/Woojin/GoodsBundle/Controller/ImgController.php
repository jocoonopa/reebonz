<?php

namespace Woojin\GoodsBundle\Controller;

use Woojin\GoodsBundle\Entity\Img;
use Woojin\GoodsBundle\Entity\GoodsPassport;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/img")
 */
class ImgController extends Controller
{
    /**
     * @Route("/{id}/upload", requirements={"id" = "\d+"}, name="img_upload", options={"expose"=true})
     * @ParamConverter("goods_passport", class="WoojinGoodsBundle:GoodsPassport")
     * @Method("POST")
     */
    public function uploadAction(Request $request, GoodsPassport $goodsPassport, $id)
    {
        /**
         * 取得目前使用者
         * @var object
         */
        $user = $this->get('security.context')->getToken()->getUser();

        /**
         * 取得上傳檔案(圖片)
         * @var object
         */
        $files = $request->files->get('file');

        // 檔案為空則返回
        if (empty($files)) {
            return new Response('');
        }

        /**
         * 檔案附檔名
         * 
         * ps: 應該是 jpg,png,gif 等圖檔，因為是內部系統我就不特別作驗證檢查了。
         * 真的要驗證，還要導入gd library 重組圖片，太麻煩了
         * 
         * @var string
         */
        //$ext = pathinfo($files->getClientOriginalName(), PATHINFO_EXTENSION);

        /**
         * 檔案名稱
         * 
         * @var string
         */
        $imgName = $id . '.' . $files->getClientOriginalName();

        /**
         * 資料夾相對路徑
         *
         * @var string
         */     
        $relativePath = '/img/' . date('Y-m-d');

        /**
         * 資料夾絕對路徑
         *
         * @var string
         */
        $absolutePath = $request->server->get('DOCUMENT_ROOT') . $relativePath;

        /**
         * 存在資料庫中的 imgpath
         * @var string
         */
        $imgpath = $relativePath . '/' . $imgName;

        // 根據使用者個的csrf值當做檔名進行移動
        if ($files->move($absolutePath, $imgName)) {
            // 刷新使用者csrf
            $user->setCsrf(uniqid());

            // 設定圖片路徑
            $goodsPassport->setImgpath($imgpath);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->persist($goodsPassport);
            $em->flush();

            // 回傳圖片路徑
            return new Response($imgpath);
        }
    }

    /**
     * @Route("/{ids}/upload/multi", name="img_upload_multi", options={"expose"=true})
     * @Method("POST")
     */
    public function uploadMulti(Request $request, $ids)
    {
        /**
         * id 陣列
         * @var array
         */
        $ids = json_decode($ids, true);

        /**
         * 商品實體陣列
         * @var array{object}
         */
        $goodsPassports = $this->getDoctrine()->getRepository('WoojinGoodsBundle:GoodsPassport')->findByIds($ids);

        /**
         * 取得目前使用者
         * @var object
         */
        $user = $this->get('security.context')->getToken()->getUser();

        /**
         * 取得上傳檔案(圖片)
         * @var object
         */
        $files = $request->files->get('file');

        // 檔案為空則返回
        if (empty($files)) {
            return new Response('');
        }

        /**
         * 檔案附檔名
         * 
         * ps: 應該是 jpg,png,gif 等圖檔，因為是內部系統我就不特別作驗證檢查了。
         * 真的要驗證，還要導入gd library 重組圖片，太麻煩了
         * 
         * @var string
         */
        $ext = pathinfo($files->getClientOriginalName(), PATHINFO_EXTENSION);

        /**
         * 檔案名稱
         * 
         * @var string
         */
        $imgName = $ids[0] . '.' . $ext;

        /**
         * 資料夾相對路徑
         *
         * @var string
         */     
        $relativePath = '/img/' . date('Y-m-d');

        /**
         * 資料夾絕對路徑
         *
         * @var string
         */
        $absolutePath = $request->server->get('DOCUMENT_ROOT') . $relativePath;

        /**
         * 存在資料庫中的 imgpath
         * @var string
         */
        $imgpath = $relativePath . '/' . $imgName;

        // 根據使用者個的csrf值當做檔名進行移動
        if ($files->move($absolutePath, $imgName)) {
            // 刷新使用者csrf
            $user->setCsrf(uniqid());

            $em = $this->getDoctrine()->getManager();

            // 設定圖片路徑
            foreach ($goodsPassports as $goodsPassport) {
                $goodsPassport->setImgpath($imgpath);
                $em->persist($goodsPassport);
            }
           
            $em->persist($user);
            $em->flush();

            // 回傳圖片路徑
            return new Response($imgpath);
        }
    }
}
