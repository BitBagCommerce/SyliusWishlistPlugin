@wishlist
Feature: Copying selected products to other wishlists
  In order to copy products to other wishlist
  As a Visitor
  I want to be able to copy products to other wishlist

  Background:
    Given the store operates on a single channel in "United States"
    Given I am on "/"
    And the store has a wishlist named "Wishlist1"
    And the store has a wishlist named "Wishlist2"
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And all store products appear under a main taxonomy

  @ui
  Scenario: Copy selected products to other wishlist
    And I should have 0 products in selected wishlist "Wishlist1"
    And I add "Jack Daniels Gentleman" to selected wishlist "Wishlist2"
    Then I should have "Jack Daniels Gentleman" in selected wishlists "Wishlist2"
    When I check "Jack Daniels Gentleman"
    And I copy selected products to "Wishlist1"
    Then I should have "Jack Daniels Gentleman" product in my wishlist