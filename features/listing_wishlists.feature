@wishlist
Feature: Listing wishlists
    In order list all available wishlists
    As a Visitor
    I want to be able to see list of my wishlists

    Background:
        Given the store operates on a single channel in "United States"
        Given I am on "/"

    @ui
    Scenario: Listing wishlist
        And the store has a wishlist named "Wishlist1"
        And the store has a wishlist named "Wishlist2"
        When I go to "/wishlists"
        Then I should have 3 wishlists