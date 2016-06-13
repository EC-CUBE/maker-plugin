<?php

namespace Plugin\Maker\Tests\Entity;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Plugin\Maker\Entity\Maker;
use Plugin\Maker\Entity\MakerPlugin;
use Plugin\Maker\Entity\ProductMaker;
class UnitTest extends AbstractAdminWebTestCase
{
    const  MAKER_NAME = 'eccube_maker_name';
    const  MAKER_URL = 'https://www.eccube.co.jp/';
    /**
     * メーカー作成画面のルーティング
     */
    public function testRoutingCreateMaker()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_maker')
        );
        $this->assertTrue($this->client->getResponse()->isSuccessful());
    }

    /**
     * メーカー編集
     */
    public function testEditMaker()
    {
        $this->createMakerByPostSubmit();
        $maker_id = $this->getMakerByName();
        $Maker = $this->app['eccube.plugin.maker.repository.maker']->find($maker_id);
        $this->expected = self::MAKER_NAME;
        $this->actual = $Maker->getName();
        $this->verify();
    }

    /**
     * メーカー削除
     */
    public function testDeleteMaker()
    {
        $this->createMakerByPostSubmit();
        $maker_id = $this->getMakerByName();
        $Maker = $this->app['eccube.plugin.maker.repository.maker']->find($maker_id);
        $status = $this->app['eccube.plugin.maker.repository.maker']->delete($Maker);
        $this->assertTrue($status);
    }

    /**
     * 商品登録画面にメーカー一緒に登録するテスト
     */
    public function testProductMaker()
    {
        $Product = $this->createProductMaker();
        $ProductMaker = $this->app['eccube.plugin.maker.repository.product_maker']->find($Product->getId());
        $this->expected = self::MAKER_URL;
        $this->actual = $ProductMaker->getMakerUrl();
        $this->verify();
    }

    /**
     * フロントにメーカーを表示するテスト
     */
    public function testMakerDisplay()
    {
        try{
            $Product = $this->createProductMaker();
            $crawler = $this->client->request(
                'GET',
                $this->app->url('product_detail', array('id' => $Product->getId()))
            );
            $crawler->filter('#maker_code')->text();
            $this->assertTrue(true);
        }catch(\InvalidArgumentException $e){
            $this->assertTrue(false);
        }
    }

    /**
     * 商品登録画面にメーカーを登録する
     */
    public function createProductMaker(){
        //create a product and submit this with maker
        $Product = $this->createProduct();
        $this->createMakerByPostSubmit();
        $maker_id = $this->getMakerByName();
        $formData = $this->createFormData($maker_id);
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_product_product_edit', array('id' => $Product->getId())),
            array('admin_product' => $formData)
        );
        return $Product;
    }

    /**
     * カテゴリコンテンツのPOST Submit
     */
    public function createMakerByPostSubmit()
    {
        //add new maker what have id is 'test_maker'
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_maker'),
            array('admin_maker' => array(
                '_token' => 'dummy',
                'name' => self::MAKER_NAME
            ))
        );
    }

    /**
     * メーカー名でメーカー取得
     */
    public function getMakerByName()
    {
        $maker_id = $this->app['eccube.plugin.maker.repository.maker']->findOneBy(array('name' => self::MAKER_NAME))->getId();
        return $maker_id;
    }

    /**
     * 商品メーカーフォーム
     */
    public function createFormData($maker_id)
    {
        $faker = $this->getFaker();
        $form = array(
            'class' => array(
                'product_type' => 1,
                'price01' => $faker->randomNumber(5),
                'price02' => $faker->randomNumber(5),
                'stock' => $faker->randomNumber(3),
                'stock_unlimited' => 0,
                'code' => $faker->word,
                'sale_limit' => null,
                'delivery_date' => ''
            ),
            'name' => $faker->word,
            'product_image' => null,
            'description_detail' => $faker->text,
            'description_list' => $faker->paragraph,
            'Category' => null,
            //'tag' => $faker->word,
            'Tag' => array(1),
            'search_word' => $faker->word,
            'free_area' => $faker->text,
            'Status' => 1,
            'note' => $faker->text,
            'tags' => null,
            'images' => null,
            'add_images' => null,
            'delete_images' => null,
            'maker' =>  $maker_id,
            'maker_url' => self::MAKER_URL,
            '_token' => 'dummy',
        );
        return $form;
    }


}
