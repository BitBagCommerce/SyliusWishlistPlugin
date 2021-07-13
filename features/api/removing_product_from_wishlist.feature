@api_wishlist
Feature: Removing product from the wishlist
    Background:
        Given the store operates on a single channel in "United States"

    @api
    Scenario: Removing product from the wishlist as an anonymous user
        Given user has a wishlist
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        When user adds product "Jack Daniels Gentleman" to the wishlist
        And user removes product "Jack Daniels Gentleman" from the wishlist
        Then user should have an empty wishlist

    @api
    Scenario: Removing product from the wishlist as an authenticated user
        Given there is a user "test@example.com"
        And user "test@example.com" "sylius" is authenticated
        Given user has a wishlist
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        When user adds product "Jack Daniels Gentleman" to the wishlist
        And user removes product "Jack Daniels Gentleman" from the wishlist
        Then user should have an empty wishlist

    @api
    Scenario: Anonymous user tries to remove product from another user's wishlist
        Given there is a user "test@example.com"
        And user "test@example.com" "sylius" is authenticated
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        When user has a wishlist
        And user is unauthenticated
        Then user tries to remove product "Jack Daniels Gentleman" from the wishlist

    @api
    Scenario: Authenticated user tries to remove product from another user's wishlist
        Given there is a user "test@example.com"
        And user "test@example.com" "sylius" is authenticated
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        When user has a wishlist
        And there is a user "test1@example.com"
        And user "test1@example.com" "sylius" is authenticated
        Then user tries to remove product "Jack Daniels Gentleman" from the wishlist
