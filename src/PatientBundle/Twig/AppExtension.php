<?php

namespace PatientBundle\Twig;

use Twig_Extension;
use  Twig_SimpleFilter;

class AppExtension extends Twig_Extension
{
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter("baseConvert", array($this, "baseConvert")),
        );
    }

    public function baseConvert($num)
    {
        $num = (string)$num;
        if ($num[2] == 5) {
            $num[2] = 3;
        }

        $result = [];
        $j = 0;
        for ($i = 0; $i != 5; $i++) {
            if ($i == 2) {
                $result[$i] = ':';
            } else {
                $result[$i] = $num[$j];
                $j++;
            }
        }
        $result = implode('', $result);
        return $result;
    }

    public function getName()
    {
        return 'app_extension';
    }
}
