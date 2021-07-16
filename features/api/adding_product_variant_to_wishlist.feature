@api_wishlist
Feature: Adding a product variant to wishlist

    Background:
        Given the store operates on a single channel in "United States"

    @api
    Scenario: Adding a product variant to wishlist as an anonymous user
        Given user has a wishlist
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
        When user adds "700ML" product variant to the wishlist
        Then user should have "700ML" product variant in the wishlist

    @api
    Scenario: Adding a product variant to wishlist as an authenticated user
        Given there is a user "test@example.com"
        And user "test@example.com" "sylius" is authenticated
        And user has a wishlist
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
        When user adds "700ML" product variant to the wishlist
        Then user should have "700ML" product variant in the wishlist

    @api
    Scenario: Anonymous user tries to add product variant to another user's wishlist
        Given there is a user "test@example.com"
        And user "test@example.com" "sylius" is authenticated
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
        When user has a wishlist
        And user is unauthenticated
        Then user tries to add "700ML" product variant to the wishlist

    @api
    Scenario: Authenticated user tries to add product to another user's wishlist
        Given there is a user "test@example.com"
        And user "test@example.com" "sylius" is authenticated
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
        When user has a wishlist
        And there is a user "test1@example.com"
        And user "test1@example.com" "sylius" is authenticated
        Then user tries to add "700ML" product variant to the wishlist
