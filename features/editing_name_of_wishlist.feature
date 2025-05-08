@wishlist
Feature: Editing wishlists name
  In order to indicate what is inside wishlist
  As a Visitor
  I want to be able to edit wishlist name

  Background:
    Given the store operates on a single channel in "United States"
    Given I am on "/"

  @ui @javascript
  Scenario: Editing wishlists name
    And the store has a wishlist named "Wishlist1"
    When I go to "/wishlists"
    Then I should see "Wishlist1"
    When I press "wishlist-edit-button-Wishlist1"
    And I fill in "edit_wishlist_name" with "Wishlist2"
    When I press "edit_wishlist_save"
    Then I go to "/wishlists"
    And I should see "Wishlist2"
