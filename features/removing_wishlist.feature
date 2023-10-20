@wishlist
Feature: Removing a wishlist
    In order to remove redundant wishlists
    As a Visitor
    I want to be able to delete wishlist

    Background:
        Given the store operates on a single channel in "United States"
        Given I am on "/wishlist"

    @ui @javascript
    Scenario: Removing a wishlist
        And the store has a wishlist named "Wishlist1"
        And the store has a wishlist named "Wishlist2"
        When I go to "/wishlists"
        Then I should have 2 wishlists
        When I press "wishlist-delete-button-Wishlist1"
        When I press "remove_wishlist_save"
        Then I should be on "/wishlists"
        Then I should wait for one second
        Then I should have 1 wishlists


    @ui @javascript
    Scenario: Removing a wishlist with one existing
        And the store has a wishlist named "Wishlist1"
        When I go to "/wishlists"
        Then I should have 1 wishlists
        When I press "wishlist-delete-button-Wishlist1"
        When I press "remove_wishlist_save"
        Then I should be on "/wishlists"
        Then I should wait for one second
        Then I should have 0 wishlists
