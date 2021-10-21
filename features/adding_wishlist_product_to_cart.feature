@wishlist
Feature: Adding wishlist product to cart
    In order to buy products I like
    As a Visitor
    I want to be able to add my wishlist product to my cart

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Adding a wishlist product to cart
        Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And all store products appear under a main taxonomy
        And the store has a product "Bushmills Black Bush Whiskey" priced at "$230.00"
        And I have these products in my wishlist
        When I go to the wishlist page
        And I select 1 quantity of "Bushmills Black Bush Whiskey" product
        And I add my wishlist products to cart
        Then I should have "Bushmills Black Bush Whiskey" product in my cart

    @ui
    Scenario: Adding a wishlist product with insufficient stock to cart
        Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And the product "Jack Daniels Gentleman" is out of stock
        And I have this product in my wishlist
        When I go to the wishlist page
        And I select 1 quantity of "Jack Daniels Gentleman" product
        And I add my wishlist products to cart
        Then I should not be notified that "Jack Daniels Gentleman" does not have sufficient stock

    @ui
    Scenario: Adding selected wishlist products to cart
        Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And all store products appear under a main taxonomy
        And the store has a product "Bushmills Black Bush Whiskey" priced at "$230.00"
        And I have these products in my wishlist
        When I go to the wishlist page
        And I select 1 quantity of "Bushmills Black Bush Whiskey" product
        And I check "Bushmills Black Bush Whiskey"
        And I select "/wishlist/selected/add" from "wishlist_actions"
        And I add selected products to cart
        Then I open page "/en_US/cart/"
        And this item should have name "Bushmills Black Bush Whiskey"
        And my cart items total should be "$230.00"









