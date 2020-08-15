<?php
/**
 * Created by PhpStorm.
 * User: SHYAM
 * Date: 18-12-2017
 * Time: 23:20
 */
namespace Plugins\Coupon;

// Disable direct access
if (!defined('APP_VERSION'))
    die("Yo, what's up?");

/**
 * Coupons model
 *
 * @version 1.0
 * @author Onelab <hello@onelab.co>
 *
 */
class CouponsModel extends \DataList
{
    /**
     * Initialize
     */
    public function __construct()
    {
//        $this->setQuery(\DB::table(TABLE_PREFIX."coupons")->select(["*",TABLE_PREFIX."coupons.*"]));
        $this->setQuery(\DB::table(TABLE_PREFIX."coupons"));
//        $this->setQuery(\DB::table(TABLE_PREFIX."coupons")->addTablePrefix(TABLE_PREFIX."coupons"));
    }
}