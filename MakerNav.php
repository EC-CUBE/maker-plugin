<?php
namespace Plugin\Maker;

use Eccube\Common\EccubeNav;

class MakerNav implements EccubeNav
{
    /**
     * {@inheritdoc}
     *
     * @return array
     */
    public static function getNav()
    {
        return [
            'product' => [
                'id' => 'maker',
                'name' => 'maker.sub_title',
                'url' => 'admin_plugin_maker_index'
            ]
        ];
    }
}