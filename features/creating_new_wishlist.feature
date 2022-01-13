@wishlist
Feature: Creating a new wishlist
    In order to create new wishlist
    As a visitor
    I want to be able to create new wishlists

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Creating a default wishlist by subscriber
        Given I am on "/"
        When I go to "/wishlists"
        Then I should have 1 wishlists

    @ui
    Scenario: Creating a new wishlist
        Given I am on "/wishlists/create"
        And I fill in "create_new_wishlist_name" with "WishlistName"
        When I press "create_new_wishlist_save"
        Then I should be on "/wishlists"
        And I should see "New wishlist has been created."
