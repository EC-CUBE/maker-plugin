<?php
/*
* This file is part of EC-CUBE
*
* Copyright(c) 2000-2015 LOCKON CO.,LTD. All Rights Reserved.
* http://www.lockon.co.jp/
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/
namespace Plugin\Maker\Tests\Entity;

use Plugin\Maker\Entity\ProductMaker;
use Eccube\Tests\EccubeTestCase;

/**
 * Plugin Entity Test
 *
 * @author Lam
 */
class ProductMakerTest extends EccubeTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testConstructor()
    {
        $ProductMaker = new ProductMaker();
        $this->expected = 0;
        $this->actual = $ProductMaker->getId();
        $this->verify();
    }

    public function testSetId()
    {
        $ProductMaker = new ProductMaker();
        $this->expected = 1;
        $ProductMaker->setId($this->expected);
        $this->actual = $ProductMaker->getId();
        $this->verify();
    }
}
