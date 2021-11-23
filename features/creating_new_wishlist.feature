@wishlist
Feature: Creating a new wishlist
    In order to create new wishlist
    As a visitor
    I want to be able to create new wishlists

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Creating a new wishlist
        Given I am on "/wishlists/create"
        And I fill the wishlist name with "Favorite"
        And I save it
        Then I should be notified that the new wishlist was created
