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

use Plugin\Maker\Entity\Maker;
use Eccube\Tests\EccubeTestCase;

/**
 * Plugin Entity Test
 *
 * @author Lam
 */
class MakerTest extends EccubeTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testConstructor()
    {
        $Maker = new Maker();
        $this->expected = 0;
        $this->actual = $Maker->getId();
        $this->verify();
    }

    public function testSetId()
    {
        $Maker = new Maker();
        $this->expected = 1;
        $Maker->setId($this->expected);
        $this->actual = $Maker->getId();
        $this->verify();
    }
}
