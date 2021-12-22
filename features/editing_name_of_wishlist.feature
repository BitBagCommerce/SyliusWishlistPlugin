@wishlist
Feature: Editing wishlists name
    In order to indicate what is inside wishlist
    As a Visitor
    I want to be able to edit wishlist name

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Editing wishlists name
        Given I am on "/"
        Given the store has a wishlist named "Wishlist1"
        When I go to "/wishlists"
        Then I should see "Wishlist1"
        And I follow edit for "Wishlist1"
        And I fill in "edit_wishlist_name_name" with "Wishlist2"
        When I press "edit_wishlist_name_save"
        Then I should be on "/wishlists"
        And I should see "Wishlist2"
