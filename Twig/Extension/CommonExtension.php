<?php

namespace TNQSoft\CommonBundle\Twig\Extension;

/**
 * Class AppExtension
 *
 * @package TNQSoft\CommonExtension\Twig\Extension
 */
class CommonExtension extends \Twig_Extension
{
    /**
     * @return array
     */
    public function getFilters()
    {
        return array(
            new \Twig_SimpleFilter('strpad', array($this, 'strpadFilter')),
            new \Twig_SimpleFilter('showDepth', array($this, 'showDepthFilter')),
            new \Twig_SimpleFilter('priceDisplay', array($this, 'priceDisplayFilter')),
            new \Twig_SimpleFilter('jsonDecode', array($this, 'jsonDecodeFilter')),
        );
    }

    /**
     * @param $string
     * @param int $count
     * @param string $symbol
     * @param string $position
     * @param bool $keepLen
     * @return mixed
     */
    public function strpadFilter($string, $count = 0, $symbol = '-', $position = 'L', $keepLen = false)
    {
        switch ($position) {
            case 'L':
                $posConst = STR_PAD_LEFT;
                break;
            case 'R':
                $posConst = STR_PAD_RIGHT;
                break;
            case 'B':
                $posConst = STR_PAD_BOTH;
                break;
            default:
                $posConst = STR_PAD_LEFT;
                break;
        }

        $len = (true === $keepLen)?intval($count)+mb_strlen($string):intval($count);

        $newString = $this->mb_str_pad($string, $len, $symbol, $posConst);

        return $newString;
    }

    /**
     * @param $string
     * @param int $depth
     * @param string $symbol
     * @return string
     */
    public function showDepthFilter($string, $depth = 0, $symbol = '-')
    {
        if($depth < 0) {
            $depth = 0;
        }

        return str_repeat($symbol, $depth).$string;
    }

    /**
     * @param $price
     * @param string $symbol
     * @return string
     */
    public function priceDisplayFilter($price, $symbol='đ')
    {
        $formatNumber = number_format($price, 0, ',', '.');
        return $formatNumber.' '.$symbol;
    }

    /**
     * @param $string
     * @param bool $assoc
     * @return array|null
     */
    public function jsonDecodeFilter($string, $assoc=false)
    {
        if(empty($string)) {
            if($assoc === true) {
                return array();
            } else {
                return null;
            }
        }

        return json_decode($string, $assoc);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'app_extension';
    }

    ////////////////////////////////////////////////////////////////////////////

    /**
     * @param $input
     * @param $pad_length
     * @param string $pad_string
     * @param int $pad_type
     * @param null $encoding
     * @return mixed
     */
    private function mb_str_pad($input, $pad_length, $pad_string = ' ', $pad_type = STR_PAD_RIGHT, $encoding = null)
    {
        if (!$encoding) {
            $diff = strlen($input) - mb_strlen($input);
        }
        else {
            $diff = strlen($input) - mb_strlen($input, $encoding);
        }
        return str_pad($input, $pad_length + $diff, $pad_string, $pad_type);
    }
}
