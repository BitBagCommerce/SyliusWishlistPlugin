@wishlist
Feature: Adding a product to selected wishlist
    In order to compare or buy products later
    As a Visitor
    I want to be able to add products to one of my wishlists

    Background:
        Given the store operates on a single channel in "United States"
        Given I am on "/"

    @ui
    Scenario: Adding a product to selected wishlist
        And the store has a wishlist named "Wishlist1"
        And the store has a wishlist named "Wishlist2"
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And all store products appear under a main taxonomy
        And I add "Jack Daniels Gentleman" to selected wishlist "Wishlist2"
        And I should have "Jack Daniels Gentleman" in selected wishlists "Wishlist2"
