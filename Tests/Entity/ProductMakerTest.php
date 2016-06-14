<?php

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
