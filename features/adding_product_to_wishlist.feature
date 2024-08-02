@wishlist @api_wishlist
Feature: Adding a product to wishlist
  In order to compare or buy products later
  As a Visitor
  I want to be able to add products to my wishlist

  Background:
    Given the store operates on a single channel in "United States"

  @ui
  Scenario: Adding a product to wishlist
    Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And all store products appear under a main taxonomy
    When I add this product to wishlist
    Then I should be notified that the product has been successfully added to my wishlist
    And I should have one item in my wishlist

  @ui
  Scenario: Adding a product variant to wishlist
    Given the store has a product "Some other whiskey" priced at "$25.00"
    And all store products appear under a main taxonomy
    When I view product "Some other whiskey"
    And I add this product to wishlist
    Then I should be notified that the product has been successfully added to my wishlist
    And I should have one item in my wishlist

  @ui
  Scenario: Adding a product as anon user and signing in
    Given the store has a product "Jimmy Beammy" priced at "$233.00"
    And the store has a product "Ice ball" priced at "$144.00"
    And all store products appear under a main taxonomy
    When I add "Jimmy Beammy" product to my wishlist
    And I log in to my account which already has "Ice ball" product in the wishlist
    Then I should have 2 products in my wishlist

  @ui
  Scenario: Adding a wishlist product signing in and out and in
    Given the store has a product "Red Roses" priced at "$12.00"
    And I have this product in my wishlist
    When I log in
    And I log out
    And I log in again
    Then I should have one item in my wishlist

  @api
  Scenario: Adding a product to wishlist with API as an anonymous user
    Given user has a wishlist
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    When user adds product "Jack Daniels Gentleman" to the wishlist
    Then user should have product "Jack Daniels Gentleman" in the wishlist

  @api
  Scenario: Adding a product to wishlist with API as an authenticated user
    Given there is a user "test@example.com"
    And user "test@example.com" "sylius" is authenticated
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    When user has a wishlist
    And  user adds product "Jack Daniels Gentleman" to the wishlist
    Then user should have product "Jack Daniels Gentleman" in the wishlist

  @api
  Scenario: Anonymous user tries to add product to another user's wishlist with API
    Given there is a user "test@example.com"
    And user "test@example.com" "sylius" is authenticated
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    When user has a wishlist
    And user is unauthenticated
    Then user tries to add product "Jack Daniels Gentleman" to the wishlist

  @api
  Scenario: Authenticated user tries to add product to another user's wishlist with API
    Given there is a user "test@example.com"
    And user "test@example.com" "sylius" is authenticated
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    When user has a wishlist
    And there is a user "test1@example.com"
    And user "test1@example.com" "sylius" is authenticated
    Then user tries to add product "Jack Daniels Gentleman" to the wishlist

  @api
  Scenario: Adding a product variant to wishlist with API as an anonymous user
    Given user has a wishlist
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
    When user adds "700ML" product variant to the wishlist
    Then user should have "700ML" product variant in the wishlist

  @api
  Scenario: Adding a product variant to wishlist with API as an authenticated user
    Given there is a user "test@example.com"
    And user "test@example.com" "sylius" is authenticated
    And user has a wishlist
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
    When user adds "700ML" product variant to the wishlist
    Then user should have "700ML" product variant in the wishlist

  @api
  Scenario: Anonymous user tries to add product variant to another user's wishlist with API
    Given there is a user "test@example.com"
    And user "test@example.com" "sylius" is authenticated
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
    When user has a wishlist
    And user is unauthenticated
    Then user tries to add "700ML" product variant to the wishlist

  @api
  Scenario: Authenticated user tries to add product to another user's wishlist with API
    Given there is a user "test@example.com"
    And user "test@example.com" "sylius" is authenticated
    And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And the product "Jack Daniels Gentleman" has a "700ML" variant priced at "$10.00"
    When user has a wishlist
    And there is a user "test1@example.com"
    And user "test1@example.com" "sylius" is authenticated
    Then user tries to add "700ML" product variant to the wishlist
