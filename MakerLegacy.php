<?php
/*
 * This file is part of the Maker plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Maker;

use Eccube\Common\Constant;
use Plugin\Maker\Entity\ProductMaker;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class Maker.
 * @deprecated Support since EC-CUBE version 3.0.0-3.0.8 and remove in version 3.1
 */
class MakerLegacy
{
    private $app;

    /**
     * Maker constructor.
     *
     * @param \Eccube\Application $app
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Add product trigger.
     *
     * @param FilterResponseEvent $event
     */
    public function onAdminProduct(FilterResponseEvent $event)
    {
        $app = $this->app;
        if (!$this->app->isGranted('ROLE_ADMIN')) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();
        $id = $request->attributes->get('id');

        list($html, $form) = $this->getHtml($request, $response, $id);
        $response->setContent($html);

        $event->setResponse($response);

        if ($form->isSubmitted()) {
            // RedirectResponseかどうかで判定する.
            if (!$response instanceof RedirectResponse) {
                return;
            }

            if ($form->isValid()) {
                /*
                 * @var ArrayCollection
                 */
                $arrMaker = $this->app['eccube.plugin.maker.repository.maker']->findBy(array(), array('rank' => 'DESC'));

                $Maker = $form->get('maker')->getData();
                $makerUrl = $form->get('maker_url')->getData();

                $ProductMaker = null;
                if ($id) {
                    $ProductMaker = $app['eccube.plugin.maker.repository.product_maker']->find($id);
                }

                if (!$ProductMaker) {
                    $ProductMaker = new ProductMaker();
                }

                // Get product id after add new
                if (empty($id)) {
                    $location = explode('/', $response->headers->get('location'));
                    $url = explode('/', $this->app->url('admin_product_product_edit', array('id' => '0')));
                    $diffs = array_values(array_diff($location, $url));
                    $id = $diffs[0];
                }

                if (count($arrMaker) > 0 && $Maker) {
                    $ProductMaker
                        ->setId($id)
                        ->setMakerUrl($makerUrl)
                        ->setDelFlg(Constant::DISABLED)
                        ->setMaker($Maker);
                    $app['orm.em']->persist($ProductMaker);
                    $app['orm.em']->flush();

                    return;
                }

                // 削除
                // ※setIdはなんだか違う気がする
                $app['orm.em']->remove($ProductMaker);
                $app['orm.em']->flush();
            }
        }

        return;
    }

    /**
     * Product detail render (front).
     *
     * @param FilterResponseEvent $event
     */
    public function onRenderProductsDetailBefore(FilterResponseEvent $event)
    {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $id = $request->attributes->get('id');

        $ProductMaker = null;
        if ($id) {
            // 商品メーカーマスタから設定されているなメーカー情報を取得
            $ProductMaker = $this->app['eccube.plugin.maker.repository.product_maker']->find($id);
        }

        if (!$ProductMaker) {
            return;
        }

        $Maker = $ProductMaker->getMaker();

        if (!$Maker) {
            // 商品メーカーマスタにデータが存在しないまたは削除されていれば無視する
            return;
        }

        $html = $this->renderProductDetail($response, $ProductMaker);

        $response->setContent($html);
        $event->setResponse($response);
    }

    /**
     * Render html of the product detail.
     *
     * @param Response     $response
     * @param ProductMaker $ProductMaker
     *
     * @return mixed|string
     */
    private function renderProductDetail(Response $response, ProductMaker $ProductMaker)
    {
        $parts = $this->app->renderView(
            'Maker/Resource/template/default/maker.twig',
            array(
                'maker_name' => $ProductMaker->getMaker()->getName(),
                'maker_url' => $ProductMaker->getMakerUrl(),
            )
        );

        $html = $response->getContent();

        // For old and new ec-cube version
        $search = '/(<div id="relative_category_box")|(<div class="relative_cat")/';
        $newHtml = $parts.'<div id="relative_category_box" class="relative_cat"';
        $html = preg_replace($search, $newHtml, $html);

        return $html;
    }

    /**
     * Get html product management.
     *
     * @param Request  $request
     * @param Response $response
     * @param null     $id
     *
     * @return array
     */
    private function getHtml(Request $request, Response $response, $id = null)
    {
        $ProductMaker = null;
        // Product for create builder (for version <= 3.0.8)
        $Product = null;
        if ($id) {
            $Product = $this->app['eccube.repository.product']->find($id);
            // 商品メーカーマスタから設定されているなメーカー情報を取得
            $ProductMaker = $this->app['eccube.plugin.maker.repository.product_maker']->find($id);
        }

        $builder = $this->app['form.factory']
            ->createBuilder('admin_product');
        if ($Product) {
            $builder = $this->app['form.factory']->createBuilder('admin_product', $Product);
            if ($Product->hasProductClass()) {
                $builder->remove('class');
            }
        }

        $form = $builder->getForm();

        if ($ProductMaker) {
            // 既に登録されている商品メーカー情報が設定されている場合、初期選択
            $form->get('maker')->setData($ProductMaker->getMaker());
            $form->get('maker_url')->setData($ProductMaker->getMakerUrl());
        }

        $form->handleRequest($request);

        $parts = $this->app->renderView(
            'Maker/Resource/template/admin/product_maker.twig',
            array('form' => $form->createView())
        );

        $html = $response->getContent();
        // For old and new version
        $search = '/(<div class="row hidden-xs hidden-sm")|(<div id="detail_box__footer")/';
        $newHtml = $parts.'<div id="detail_box__footer" class="row hidden-xs hidden-sm"';
        $html = preg_replace($search, $newHtml, $html);

        return array($html, $form);
    }
}
