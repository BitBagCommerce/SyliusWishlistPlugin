<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Sylius\WishlistPlugin\Behat\Context\Cli;

use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\HttpKernel\KernelInterface;
use Webmozart\Assert\Assert;

final class WishlistContext implements Context
{
    public const REMOVE_GUEST_WISHLISTS_COMMAND = 'sylius:wishlist:remove-guest-wishlists';

    private Application $application;

    private ?CommandTester $commandTester = null;

    public function __construct(
        KernelInterface $kernel,
    ) {
        $this->application = new Application($kernel);
    }

    /**
     * @When I run delete guest wishlists command
     */
    public function runRemoveGuestWishlistsCommand(): void
    {
        $command = $this->application->find(self::REMOVE_GUEST_WISHLISTS_COMMAND);

        $this->commandTester = new CommandTester($command);
        $this->commandTester->execute([]);
    }

    /**
     * @When I run delete guests wishlists command with invalid date
     */
    public function runRemoveGuestWishlistsCommandWithInvalidDate(): void
    {
        $command = $this->application->find(self::REMOVE_GUEST_WISHLISTS_COMMAND);
        $this->commandTester = new CommandTester($command);
        $this->commandTester->execute(['--date' => 'invalidDate']);
    }

    /**
     * @When the command should succeed
     */
    public function theCommandShouldSucceed(): void
    {
        Assert::isInstanceOf($this->commandTester, CommandTester::class);
        Assert::same($this->commandTester->getStatusCode(), 0);
    }

    /**
     * @When the command should fail
     */
    public function theCommandShouldFail(): void
    {
        Assert::isInstanceOf($this->commandTester, CommandTester::class);
        Assert::same($this->commandTester->getStatusCode(), 1);
    }

    /**
     * @When I run delete guests wishlists command to delete wishlists inactive for more than 5 days
     */
    public function runRemoveGuestWishlistsCommandWithDate5DaysAgo(): void
    {
        $date = new \DateTime();
        $date->modify('-5 days');
        $date = $date->format('d-m-Y');

        $command = $this->application->find(self::REMOVE_GUEST_WISHLISTS_COMMAND);
        $this->commandTester = new CommandTester($command);
        $this->commandTester->execute(['--date' => $date]);
    }
}
