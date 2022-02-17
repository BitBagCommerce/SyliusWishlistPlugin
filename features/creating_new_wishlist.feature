@wishlist
Feature: Creating a new wishlist
    In order to create new wishlist
    As a visitor
    I want to be able to create new wishlists

    Background:
        Given the store operates on a single channel in "United States"
        Given I am on "/"

    @ui
    Scenario: Creating a default wishlist by subscriber
        When I go to "/wishlists"
        Then I should have 1 wishlists

    @ui @javascript
    Scenario: Creating a new wishlist
        When I go to "/wishlists"
        Then I should have 1 wishlists
        When I press "create_new_wishlist_button"
        And I fill in "create_new_wishlist_name" with "WishlistName"
        Then I press "create_new_wishlist_save"
        Then I should wait for one second
        Then I should be on "/wishlists"
        And I should have 2 wishlists
