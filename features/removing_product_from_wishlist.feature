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
