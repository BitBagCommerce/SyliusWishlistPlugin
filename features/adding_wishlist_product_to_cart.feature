@wishlist
Feature: Adding wishlist product to cart
    In order to buy products I like
    As a Visitor
    I want to be able to add my wishlist product to my cart

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Removing a product from wishlist
        Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And all store products appear under a main taxonomy
        And the store has a product "Bushmills Black Bush Whiskey" priced at "$230.00"
        And I have these products in my wishlist
        When I go to the wishlist page
        And I select 1 quantity of "Bushmills Black Bush Whiskey" product
        And I add my wishlist products to cart
        Then I should have "Bushmills Black Bush Whiskey" product in my cart
