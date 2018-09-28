<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) LOCKON CO.,LTD. All Rights Reserved.
 *
 * http://www.lockon.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Plugin\Maker4;

use Eccube\Common\EccubeTwigBlock;

class MakerTwigBlock implements EccubeTwigBlock
{
    public static function getTwigBlock()
    {
        return ['@Maker4/default/maker.twig'];
    }
}
