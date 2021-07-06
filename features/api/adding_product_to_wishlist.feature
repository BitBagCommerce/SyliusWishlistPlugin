@api_wishlist
Feature: Adding a product to wishlist
    Background:
        Given the store operates on a single channel in "United States"

    @api
    Scenario: Adding a product to wishlist as an anonymous user
        Given user has a wishlist
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        When user adds product "Jack Daniels Gentleman" to the wishlist
        Then user should have product "Jack Daniels Gentleman" in the wishlist

    @api
    Scenario: Adding a product variant to wishlist as an anonymous user
        Given user has a wishlist
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
        When user adds "700ML" product variant to the wishlist

    @api
    Scenario: Adding a product to wishlist as an authenticated user
        Given there is a user "test@example.com"
        And user "test@example.com" "sylius" is authenticated
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        When user has a wishlist
        And  user adds product "Jack Daniels Gentleman" to the wishlist

