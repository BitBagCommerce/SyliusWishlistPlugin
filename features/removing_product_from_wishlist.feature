@wishlist @api_wishlist
Feature: Removing a product from wishlist
  In order to compare or buy products later
  As a Visitor
  I want to be able to remove products to my wishlist

  Background:
    Given the store operates on a single channel in "United States"

  @ui
  Scenario: Removing a product from wishlist
    Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And I have this product in my wishlist
    When I go to the wishlist page
    And I remove this product
    Then I should be notified that the product has been removed from my wishlist
    And I should have 0 products in my wishlist

  @ui
  Scenario: Removing selected products from wishlist
    Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And I have this product in my wishlist
    When I go to the wishlist page
    Then I check "Jack Daniels Gentleman"
    And I remove selected products from wishlist
    And I should have 0 products in my wishlist

  @api
  Scenario: Removing product from the wishlist with API as an anonymous user
    Given user has a wishlist
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    When user adds product "Jack Daniels Gentleman" to the wishlist
    And user removes product "Jack Daniels Gentleman" from the wishlist
    Then user should have an empty wishlist

  @api
  Scenario: Removing product from the wishlist with API as an authenticated user
    Given there is a user "test@example.com"
    And user "test@example.com" "sylius" is authenticated
    Given user has a wishlist
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    When user adds product "Jack Daniels Gentleman" to the wishlist
    And user removes product "Jack Daniels Gentleman" from the wishlist
    Then user should have an empty wishlist

  @api
  Scenario: Anonymous user tries to remove product from another user's wishlist with API
    Given there is a user "test@example.com"
    And user "test@example.com" "sylius" is authenticated
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    When user has a wishlist
    And user is unauthenticated
    Then user tries to remove product "Jack Daniels Gentleman" from the wishlist

  @api
  Scenario: Authenticated user tries to remove product from another user's wishlist with API
    Given there is a user "test@example.com"
    And user "test@example.com" "sylius" is authenticated
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    When user has a wishlist
    And there is a user "test1@example.com"
    And user "test1@example.com" "sylius" is authenticated
    Then user tries to remove product "Jack Daniels Gentleman" from the wishlist

  @api
  Scenario: Removing product variant from wishlist with API as an anonymous user
    Given user has a wishlist
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
    When user adds "700ML" product variant to the wishlist
    Then user removes "700ML" product variant from the wishlist
    Then user should have an empty wishlist

  @api
  Scenario: Removing a product variant from wishlist with API as an authenticated user
    Given there is a user "test@example.com"
    And user "test@example.com" "sylius" is authenticated
    And user has a wishlist
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
    When user adds "700ML" product variant to the wishlist
    Then user removes "700ML" product variant from the wishlist
    Then user should have an empty wishlist
