@wishlist
Feature: Assigning a wishlist to a user
  In order to assign a wishlist to a user
  As a Visitor
  I want to be able to assign a wishlist to my account if I created it as a not logged in user.

  Background:
    Given the store operates on a single channel in "United States"
    And there is a customer account "jdeer@sylius.pl"
    And user "jdeer@sylius.pl" has a wishlist named "Wishlist1" with token "123456token"
    And user "jdeer@sylius.pl" has a wishlist named "Wishlist2" with token "123456token"

  @ui
  Scenario: Listing wishlists
    When I go to "/"
    And I log in as "jdeer@sylius.pl"
    And I go to "/wishlists"
    Then I should have 2 wishlists

  @ui
  Scenario: Assigning a wishlist
    Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And all store products appear under a main taxonomy
    And I add this product to wishlist
    When I go to "/"
    And I log in as "jdeer@sylius.pl"
    And I go to "/wishlists"
    And I follow "Save wishlist"
    Then I should have 3 wishlists
    And I should not see "Save wishlist"

  @ui @javascript
  Scenario: Assigning a wishlist to a user and logout
    When I go to "/"
    And I log in as "jdeer@sylius.pl"
    And I go to "/wishlists"
    And I press "wishlist-edit-button-Wishlist"
    And I fill in "edit_wishlist_name" with "Wishlist-assigned"
    And I press "edit_wishlist_save"
    And I follow "Save wishlist"
    And I log out
    And I go to "/wishlists"
    Then I should have 1 wishlists
    And I should not see "Wishlist-assigned"
    And I should see "Wishlist"

  @ui @javascript
  Scenario: Logout without assigning a wishlist to a user
    When I go to "/"
    And I log in as "jdeer@sylius.pl"
    And I go to "/wishlists"
    And I press "wishlist-edit-button-Wishlist"
    And I fill in "edit_wishlist_name" with "Wishlist-not-assigned"
    And I press "edit_wishlist_save"
    And I log out
    And I go to "/wishlists"
    Then I should have 1 wishlists
    And I should see "Wishlist-not-assigned"
