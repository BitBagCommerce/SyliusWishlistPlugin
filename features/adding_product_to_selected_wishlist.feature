@wishlist
Feature: Adding a product to selected wishlist
    In order to compare or buy products later
    As a Visitor
    I want to be able to add products to one of my wishlists

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Adding a product to selected wishlist
        Given I am on "/"
        And the store has a wishlist named "Wishlist1"
        And the store has a wishlist named "Wishlist2"
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And the store has a product "Bushmills Black Bush Whiskey" priced at "$230.00"
        And all store products appear under a main taxonomy
        When I add "Jack Daniels Gentleman" to selected wishlist "Wishlist1"
        And I add "Bushmills Black Bush Whiskey" to selected wishlist "Wishlist2"
        Then I should have "Jack Daniels Gentleman" in selected wishlists "Wishlist1"
        And I should have "Bushmills Black Bush Whiskey" in selected wishlists "Wishlist2"
