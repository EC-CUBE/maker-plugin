<?php
namespace Plugin\Maker;

class MakerTwigBlock implements \Eccube\Common\EccubeTwigBlock
{
    /**
     * @return array
     */
    public static function getTwigBlock()
    {
        return [
            '@Maker/default/Block/maker.twig'
        ];
    }

}

