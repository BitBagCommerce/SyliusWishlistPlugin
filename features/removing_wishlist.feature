@wishlist
Feature: Removing a wishlist
    In order to remove redundant wishlists
    As a Visitor
    I want to be able to delete wishlist

    Background:
        Given the store operates on a single channel in "United States"
        Given I am on "/"

    @ui
    Scenario: Removing a wishlist
        And the store has a wishlist named "Wishlist1"
        And the store has a wishlist named "Wishlist2"
        When I go to "/wishlists"
        Then I should have 3 wishlists
        When I follow remove for "Wishlist1"
        Then I should have 2 wishlists

    @ui
    Scenario: Removing a wishlist with one existing
        When I go to "/wishlists"
        Then I should have 1 wishlists
        When I follow remove for "Wishlist1"
        Then I should have 1 wishlists