<?php

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Command;

use BitBag\SyliusWishlistPlugin\Repository\WishlistRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

final class RemoveExpiredWishlistsCommand extends Command
{
    protected static $defaultName = 'bitbag:wishlist:remove-expired-wishlists';
    protected static $defaultDescription = 'Removes wishlists that have been idle for a period set in `bit_bag_sylius_wishlist.wishlist_expiration_period` configuration key.';

    private WishlistRepositoryInterface $wishlistRepository;
    private string $expirationPeriod;

    public function __construct(WishlistRepositoryInterface $wishlistRepository, string $expirationPeriod)
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->expirationPeriod = $expirationPeriod;

        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if ($io->isVerbose()) {
            $io->info(sprintf(
                'Command will remove wishlists that have been idle for %s.',
                $this->expirationPeriod
            ));
        }

        $this->wishlistRepository->deleteWishlistsNotModifiedSince(new \DateTime('-'.$this->expirationPeriod));

        if ($io->isVerbose()) {
            $io->success('Execution complete.');
        }

        return Command::SUCCESS;
    }
}
