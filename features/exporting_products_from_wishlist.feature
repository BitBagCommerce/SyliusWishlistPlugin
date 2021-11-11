@wishlist
Feature: Exporting products from wishlist
  In order to safe my wishlist
  As a Visitor
  I want to be able to export products

  Background:
    Given the store operates on a single channel in "United States"

  @ui
  Scenario: Exporting selected products to csv
    Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And all store products appear under a main taxonomy
    And the store has a product "Bushmills Black Bush Whiskey" priced at "$230.00"
    And I have these products in my wishlist
    When I go to the wishlist page
    And I select 1 quantity of "Bushmills Black Bush Whiskey" product
    And I check "Bushmills Black Bush Whiskey"
    And I export selected products to csv
    Then I should have downloaded CSV file
