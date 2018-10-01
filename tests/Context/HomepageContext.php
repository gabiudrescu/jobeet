<?php
/**
 * Created by PhpStorm.
 * User: gabiudrescu
 * Date: 02.09.2018
 * Time: 21:06
 */

namespace App\Tests\Context;

use App\Tests\Page\Homepage;
use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;

class HomepageContext implements Context
{
    /**
     * @var Homepage
     */
    private $homepage;

    public function __construct(Homepage $homepage)
    {
        $this->homepage = $homepage;
    }

    /**
     * @Given I open the homepage
     */
    public function test()
    {
        Assert::true($this->homepage->open()->isOpen());
    }
}
