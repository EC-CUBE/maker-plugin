<?php
/*
 * This file is part of the Maker plugin
 *
 * Copyright (C) 2016 LOCKON CO.,LTD. All Rights Reserved.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Plugin\Maker\Tests\Web;

use Eccube\Entity\Product;
use Faker\Generator;
use Plugin\Maker\Entity\Maker;
use Symfony\Component\HttpKernel\Client;
use Eccube\Repository\ProductRepository;

/**
 * Class ProductDetailTest
 * Hook point test
 */
class ProductDetailTest extends MakerWebCommon
{
    /**
     * @var Maker $Maker
     */
    protected $Maker;

    /**
     * @var Product $Product
     */
    protected $Product;

    /**
     * @var ProductRepository
     */
    protected $productRepository;

    /**
     * Set up function.
     */
    public function setUp()
    {
        parent::setUp();
        $this->deleteAllRows(['plg_maker']);

        $this->Maker = $this->createMaker();
        $this->Product = $this->createProductMaker($this->Maker);
        $this->productRepository = $this->container->get(ProductRepository::class);

    }

    /**
     * Product detail render test maker
     */
    public function testProductDetailWhenHasMakerButUnRegister()
    {
        $productId = $this->Product->getId();
        $this->Product->setMaker(null);
        $this->Product->setMakerUrl(null);
        $this->entityManager->persist($this->Product);
        $this->entityManager->flush();
        $crawler = $this->client->request('GET', $this->generateUrl('product_detail', ['id' => $productId]));
        $html = $crawler->filter('.ec-productRole__profile')->html();
        $this->assertNotContains('メーカーコード', $html);
        $this->assertNotContains('メーカーURL', $html);
    }

    /**
     * Product detail render test maker
     */
    public function testProductDetailWhenRegisterMakerWithoutMakerUrl()
    {
        $productId = $this->Product->getId();
        $this->Product->setMakerUrl('');
        $this->entityManager->persist($this->Product);
        $this->entityManager->flush();

        $crawler = $this->client->request('GET', $this->generateUrl('product_detail', ['id' => $productId]));

        $html = $crawler->filter('.ec-productRole__profile')->html();
        $this->assertContains($this->Product->getMaker()->getName(), $html);
        $this->assertNotContains('メーカーURL', $html);
    }

    /**
     * Product detail render test maker
     */
    public function testProductDetailWhenRegisterMakerAndMakerUrl()
    {
        $productId = $this->Product->getId();

        $crawler = $this->client->request('GET', $this->generateUrl('product_detail', ['id' => $productId]));

        $html = $crawler->filter('.ec-productRole__profile')->html();
        $this->assertContains($this->Product->getMaker()->getName(), $html);
        $this->assertContains($this->Product->getMakerUrl(), $html);
    }

    /**
     * Create maker
     *
     * @param Maker   $Maker
     * @param Product $Product
     *
     * @return Product
     */
    protected function createProductMaker(Maker $Maker, $Product = null)
    {
        /**
         * @var Generator $faker
         */
        $faker = $this->getFaker();

        if (!$Product) {
            // New product
            /**
             * @var Generator $faker
             */
            $faker = $this->getFaker();
            $formData = $this->createFormData();
            $formData['Maker'] = $Maker->getId();
            $formData['maker_url'] = $faker->url;

            /**
             * @var Client $client
             */
            $client = $this->client;
            $client->request(
                'POST',
                $this->generateUrl('admin_product_product_new'),
                ['admin_product' => $formData]
            );

            $this->assertTrue($client->getResponse()->isRedirection());

            $arrTmp = explode('/', $client->getResponse()->getTargetUrl());
            $productId = $arrTmp[count($arrTmp)-2];

            $client->followRedirect();

            $this->productRepository = $this->container->get(ProductRepository::class);
            $Product = $this->productRepository->find($productId);
        }

        return $Product;
    }
}
