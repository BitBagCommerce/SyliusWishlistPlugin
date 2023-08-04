@wishlist
Feature: Showing chosen wishlist
    In order to see products from chosen wishlist
    As a Visitor
    I want to be able to see chosen wishlist

    Background:
        Given the store operates on a single channel in "United States"
        Given I am on "/"

    @ui
    Scenario: Showing chosen wishlist
        And the store has a wishlist named "Wishlist1"
        And the store has a wishlist named "Wishlist2"
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And all store products appear under a main taxonomy
        And I add "Jack Daniels Gentleman" to selected wishlist "Wishlist2"
        Then I am on "/wishlists"
        And I should have 2 wishlists
        When I open "Wishlist2"
        Then I should see "Wishlist2"
        And I should have "Jack Daniels Gentleman" in selected wishlists "Wishlist2"
