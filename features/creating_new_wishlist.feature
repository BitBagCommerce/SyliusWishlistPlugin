@wishlist
Feature: Creating a new wishlist
    In order to create new wishlist
    As a visitor
    I want to be able to create new wishlists

    Background:
        Given the store operates on a single channel in "United States"
        Given I am on "/"

    @ui @javascript
    Scenario: Creating a new wishlist
        When I go to "/wishlists"
        When I open modal to create new wishlist
        And I set new wishlist name "WishlistName"
        Then I save new wishlist modal
        Then I should wait for one second
        Then I should be on "/wishlists"
        And I should have 1 wishlists
