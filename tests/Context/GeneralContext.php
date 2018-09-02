<?php
/**
 * Created by PhpStorm.
 * User: gabiudrescu
 * Date: 02.09.2018
 * Time: 21:22
 */

namespace App\Tests\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Context\Context;
use Behat\MinkExtension\Context\MinkContext;
use Webmozart\Assert\Assert;

class GeneralContext implements Context
{
    /**
     * @Given fixtures were generated
     */
    public function fixturesWereGenerated()
    {
        shell_exec('php bin/console hautelook:fixtures:load -n');
    }
}
