<?php

namespace Plugin\Maker\Tests\Entity;

use Plugin\Maker\Entity\MakerPlugin;
use Eccube\Tests\EccubeTestCase;

/**
 * Plugin Entity Test
 *
 * @author Lam
 */
class MakerPluginTest extends EccubeTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testConstructor()
    {
        $MakerPlugin = new MakerPlugin();
        $this->expected = 0;
        $this->actual = $MakerPlugin->getId();
        $this->verify();
    }

    public function testSetId()
    {
        $MakerPlugin = new MakerPlugin();
        $this->expected = "Code";
        $MakerPlugin->setCode($this->expected);
        $this->actual = $MakerPlugin->getCode();
        $this->verify();
    }
}
