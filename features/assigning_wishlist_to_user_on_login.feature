@wishlist
Feature: Assigning a wishlist to a user on login
  In order to assign a wishlist to a user on login
  As a Visitor
  I want to be able to assign a wishlist to my account on login if I created it as a not logged in user.

  @ui @javascript
  Scenario: Assigning a wishlist to a user after login
    Given the store operates on a single channel in "United States"
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And all store products appear under a main taxonomy
    And the store has a wishlist named "Wishlist1"
    When I go to "/"
    And I add this product to wishlist
    Then I log in
    And I remove wishlist cookie token
    When I go to "/wishlists"
    Then I should have 1 wishlists

