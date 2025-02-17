<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command;

use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'bitbag:wishlist:remove-guest-wishlists',
    description: 'Removes guest wishlists',
)]
final class RemoveGuestWishlistsCommand extends Command
{
    public function __construct(private WishlistRepositoryInterface $wishlistRepository)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'date',
                'd',
                InputOption::VALUE_OPTIONAL,
                'The date to remove wishlists updated before (format: d-m-Y)',
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $updatedAt = $input->getOption('date');
        if (null !== $updatedAt) {
            $updatedAt = \DateTime::createFromFormat('d-m-Y', $updatedAt);
            if (!$updatedAt instanceof \DateTimeInterface) {
                $output->writeln('<error>Invalid date format. Please use d-m-Y format.</error>');

                return Command::FAILURE;
            }
            $wishlists = $this->wishlistRepository->findAllAnonymousUpdatedAtEarlierThan($updatedAt);
        } else {
            $wishlists = $this->wishlistRepository->findAllByAnonymous();
        }

        foreach ($wishlists as $wishlist) {
            $this->wishlistRepository->remove($wishlist);
        }

        $output->writeln(sprintf('Removed %d guest wishlists', \count($wishlists)));

        return Command::SUCCESS;
    }
}
