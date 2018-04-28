@wishlist
Feature: Adding a product to wishlist
    In order to compare or buy products later
    As a Visitor
    I want to be able to add products to my wishlist

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Adding a product to wishlist
        Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And all store products appear under a main taxonomy
        When I add this product to wishlist
        Then I should be on my wishlist page
        And I should be notified that the product has been successfully added to my wishlist
        And there should be one item in my wishlist

    @ui
    Scenario: Adding a product as anon user and signing in
        Given the store has a product "Jimmy Beammy" priced at "$233.00"
        And the store has a product "Ice ball" priced at "$144.00"
        And all store products appear under a main taxonomy
        When I add "Jimmy Beammy" product to my wishlist
        And I log in to my account which already has "Ice ball" product in the wishlist
        Then I should have 2 products in my wishlist

    @ui
    Scenario: Adding a wishlist product signing in and out and in
        Given the store has a product "Red Roses" priced at "$12.00"
        And all store products appear under a main taxonomy
        And I have this product in my wishlist
        When I log in
        And I log out
        And I log in
        Then I should have 1 product in my wishlist
