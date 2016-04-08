<?php

namespace Plugin\Maker\Tests\Entity;

use Eccube\Tests\Web\Admin\AbstractAdminWebTestCase;
use Plugin\Maker\Entity\Maker;
use Plugin\Maker\Entity\MakerPlugin;
use Plugin\Maker\Entity\ProductMaker;
class UnitTest extends AbstractAdminWebTestCase
{
    private  $maker_name = 'eccube_maker_name';
    private  $maker_url = 'https://www.eccube.co.jp/';
    /**
     * メーカー作成画面のルーティング
     */
    public function test_routing_create_maker()
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
    public function test_edit_maker()
    {
        $this->create_maker_by_post_submit();
        //get maker from DB
        $maker_id = $this->get_maker_by_name();
        $Maker = $this->app['eccube.plugin.maker.repository.maker']->find($maker_id);
        //compare it
        $this->expected = $this->maker_name;
        $this->actual = $Maker->getName();
        $this->verify();
    }

    /**
     * メーカー削除
     */
    public function test_delete_maker()
    {
        $this->create_maker_by_post_submit();
        //get maker from DB
        $maker_id = $this->get_maker_by_name();
        $Maker = $this->app['eccube.plugin.maker.repository.maker']->find($maker_id);
        $status = $this->app['eccube.plugin.maker.repository.maker']->delete($Maker);
        $this->assertTrue($status);
    }

    /**
     * 商品登録画面にメーカー一緒に登録するテスト
     */
    public function test_product_maker()
    {
        $Product = $this->create_product_maker();
        //get maker from DB
        $ProductMaker = $this->app['eccube.plugin.maker.repository.product_maker']->find($Product->getId());
        //compare it
        $this->expected = $this->maker_url;
        $this->actual = $ProductMaker->getMakerUrl();
        $this->verify();
    }

    /**
     * フロントにメーカーを表示するテスト
     */
    public function test_maker_display()
    {
        try{
            $Product = $this->create_product_maker();
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
    public function create_product_maker(){
        //create a product and submit this with maker
        $Product = $this->createProduct();
        $this->create_maker_by_post_submit();
        $maker_id = $this->get_maker_by_name();
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
    public function create_maker_by_post_submit()
    {
        //add new maker what have id is 'test_maker'
        $crawler = $this->client->request(
            'POST',
            $this->app->url('admin_maker'),
            array('admin_maker' => array(
                '_token' => 'dummy',
                'name' => $this->maker_name
            ))
        );
    }

    /**
     * メーカー名でメーカー取得
     */
    public function get_maker_by_name()
    {
        $maker_id = $this->app['eccube.plugin.maker.repository.maker']->findOneBy(array('name' => $this->maker_name))->getId();
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
            'maker_url' => $this->maker_url,
            '_token' => 'dummy',
        );
        return $form;
    }


}
