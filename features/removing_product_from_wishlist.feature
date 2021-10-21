@wishlist
Feature: Removing a product from wishlist
    In order to compare or buy products later
    As a Visitor
    I want to be able to remove products to my wishlist

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Removing a product from wishlist
        Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And I have this product in my wishlist
        When I go to the wishlist page
        And I remove this product
        Then I should be notified that the product has been removed from my wishlist
        And I should have 0 products in my wishlist

    @ui
    Scenario: Remove selected wishlist products
        Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And all store products appear under a main taxonomy
        And the store has a product "Bushmills Black Bush Whiskey" priced at "$230.00"
        And I have these products in my wishlist
        When I go to the wishlist page
        And I check "Bushmills Black Bush Whiskey"
        And I select "/wishlist/selected/remove" from "wishlist_actions"
        And I press "wishlist_action_submit"
        Then I should have 1 products in my wishlist

