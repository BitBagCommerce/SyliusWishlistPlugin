<?php

/*
 * This file has been created by developers from BitBag.
 * Feel free to contact us once you face any issues or want to start
 * You can find more information about us on https://bitbag.io and write us
 * an email on hello@bitbag.io.
 */

declare(strict_types=1);

namespace BitBag\SyliusWishlistPlugin\Console;

use BitBag\SyliusWishlistPlugin\Remover\AnonymousWishlistsRemoverInterface;
use SyliusLabs\Polyfill\Symfony\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @final
 */
class RemoveAnonymousWishlistsCommand extends ContainerAwareCommand
{
    protected static $defaultName = 'bitbag:remove-anonymous-wishlists';

    protected function configure(): void
    {
        $this
            ->setDescription('Removes anonymous wishlists that have been idle for a period set in `bitbag_sylius_wishlist_plugin.parameters.anonymous_wishlist_expiration_period` configuration key.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var string $expirationTime */
        $expirationTime = $this->getContainer()->getParameter('bitbag_sylius_wishlist_plugin.parameters.anonymous_wishlist_expiration_period');

        if (empty($expirationTime)) {
            $output->writeln('<error>`bitbag_sylius_wishlist_plugin.parameters.anonymous_wishlist_expiration_period` configuration key is not set, so no wishlists will be removed.</error>');

            return 0;
        }

        $output->writeln(sprintf(
            'Command will remove anonymous wishlists that have been idle for <info>%s</info>.',
            (string) $expirationTime,
        ));

        /** @var AnonymousWishlistsRemoverInterface $anonymousWishlistsRemover */
        $anonymousWishlistsRemover = $this->getContainer()->get('bitbag_sylius_wishlist_plugin.services.anonymous_wishlists_remover');
        $anonymousWishlistsRemover->remove();

        return 0;
    }
}
