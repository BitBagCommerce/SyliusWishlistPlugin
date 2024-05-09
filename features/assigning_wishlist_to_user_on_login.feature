@wishlist
Feature: Assigning a wishlist to a user on login
  In order to assign a wishlist to a user on login
  As a Visitor
  I want to be able to assign a wishlist to my account on login if I created it as a not logged in user.

  Background:
    Given the store operates on a single channel in "United States"

  @ui @javascript
  Scenario: Assigning a wishlist to a user after login
    When I go to "/"
    And the store has a wishlist named "Wishlist1"
    Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And all store products appear under a main taxonomy
    When I add this product to wishlist
    Then I log in
    And I remove wishlist cookie token
    Then I go to "/wishlists"
    And I should have 1 wishlists

