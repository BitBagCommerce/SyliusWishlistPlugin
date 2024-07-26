@cli_wishlist
Feature: Removing guest wishlists
  In order to clean guest wishlists
  As a developer
  I want to be able to delete wishlists created by anonymous customers by running a CLI command

  Background:
    Given the store operates on a single channel in "United States"
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And all store products appear under a main taxonomy
    And I add this product to wishlist
    And there is 1 wishlist in the database

  @cli
  Scenario: Removing all guest wishlists
    Given there is a user "test@example.com"
    And user "test@example.com" has a wishlist
    And there are 2 wishlists in the database
    When I run delete guest wishlists command
    Then the command should succeed
    And there is 1 wishlist in the database

  @cli
  Scenario: Removing guest wishlists with date
    Given there is a guest wishlist which has been inactive for a week
    And there are 2 wishlists in the database
    When I run delete guests wishlists command to delete wishlists inactive for more than 5 days
    Then the command should succeed
    And there is 1 wishlist in the database

  @cli
  Scenario: Removing guest wishlists with invalid date
    When I run delete guests wishlists command with invalid date
    Then the command should fail
    And there is 1 wishlist in the database
