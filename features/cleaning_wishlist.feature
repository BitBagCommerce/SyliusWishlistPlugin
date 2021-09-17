@wishlist
Feature: Cleaning all wishlist
  In order to clean all wishlist
  As a Visitor
  I want to be able to clean all wishlist by one click

  Background:
    Given the store operates on a single channel in "United States"

  @ui
  Scenario: Cleaning wishlist
      Given the store has a product "Jimmy Beammy" priced at "$233.00"
      And the store has a product "Ice ball" priced at "$144.00"
      And all store products appear under a main taxonomy
      And I add "Jimmy Beammy" product to my wishlist
      And I add "Ice ball" product to my wishlist
      When I go to the wishlist page
      And I clean wishlist
      Then I should be notified that the wishlist has been cleaned

