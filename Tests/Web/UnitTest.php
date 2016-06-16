<?php

namespace Plugin\Maker\Tests\Web;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Eccube\Common\Constant;
use \Plugin\Maker\Entity\Maker;

class UnitTest extends AbstractAdminWebTestCase
{
    const MAKER_NAME = 'eccube_maker_name';
    const MAKER_URL = 'https://www.eccube.co.jp/';
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

    public function testRoutingProductNew()
    {
        $this->client->request(
            'GET',
            $this->app->url('admin_product_product_new')
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
        $Maker = $this->createMaker();
        $maker_id = $Maker->getId();
        $this->client->request(
            'POST',
            $this->app->url('admin_maker_delete', array('id' => $maker_id)),
            array('admin_maker' => array(
                '_token' => 'dummy'
            ))
        );
        $this->expected = 1;
        $this->actual = $Maker->getDelFlg();
        $this->verify();
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
     * 商品登録画面にメーカー一緒に登録してUPテスト
     */
    public function testMakerUp()
    {
        $Maker = $this->createMaker();
        $Maker1 = $this->createMaker();
        //Up maker
        $this->client->request(
            'POST',
            $this->app->url('admin_maker_up', array('id' => $Maker->getId())),
            array('admin_maker' => array(
                '_token' => 'dummy'
            ))
        );
        $this->expected = $Maker->getRank();
        $this->actual = $Maker1->getRank() + 1;
        $this->verify();
    }

    /**
     * 商品登録画面にメーカー一緒に登録してUPテスト
     */
    public function testMakerDown()
    {
        $Maker = $this->createMaker();
        $Maker1 = $this->createMaker();
        //down maker1
        $this->client->request(
            'POST',
            $this->app->url('admin_maker_down', array('id' => $Maker1->getId())),
            array('admin_maker' => array(
                '_token' => 'dummy'
            ))
        );
        $this->expected = $Maker->getRank();
        $this->actual = $Maker1->getRank() + 1;
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
            $makerUrl = $crawler->filter('.item_code a')->text();
            $this->expected = self::MAKER_URL;
            $this->actual = $makerUrl;
            $this->verify();
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
     * POST Submit
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

    public function createMaker()
    {
        $Maker = new Maker();
        $Maker->setName(self::MAKER_NAME);
        $this->app['eccube.plugin.maker.repository.maker']->save($Maker);
        return $Maker;
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
        //current version >= 3.0.10
        if(version_compare('3.0.10', Constant::VERSION, '<=')){
            $tag = array("Tag" => array(1));
        }else{
            $tag =  array("tag" => $faker->word);
        }
        $form += $tag;
        return $form;
    }

}
