@api_wishlist
Feature: Removing product variant from wishlist
    Background:
        Given the store operates on a single channel in "United States"

    @api
    Scenario: Removing product variant from wishlist as an anonymous user
        Given user has a wishlist
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
        When user adds "700ML" product variant to the wishlist
        Then user removes "700ML" product variant from the wishlist
        Then user should have an empty wishlist

    @api
    Scenario: Removing a product variant from wishlist as an authenticated user
        Given there is a user "test@example.com"
        And user "test@example.com" "sylius" is authenticated
        And user has a wishlist
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
        When user adds "700ML" product variant to the wishlist
        Then user removes "700ML" product variant from the wishlist
        Then user should have an empty wishlist
