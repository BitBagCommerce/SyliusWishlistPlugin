@api_wishlist
Feature: Adding a product to wishlist
  In order to compare or buy products later
  As a Visitor
  I want to be able to add products to my wishlist on 2 different channels

  Background:
    Given the store operates on a channel named "Web-US" in "USD" currency
    And the store also operates on another channel named "Web-EU" in "EUR" currency
    And the store has a product "Leprechaun's Gold" priced at "$100.00" available in channel "Web-US" and channel "Web-EU"
    And the store has a product "Leprechaun's Silver" priced at "€10.00" in "Web-EU" channel
    And all store products appear under a main taxonomy

  @api
  Scenario: Adding product to wishlist in the first channel checking wishlist on the second channel.
    Given I change my current channel to "Web-EU"
    And user has a wishlist in "Web-EU"
    When user adds product "Leprechaun's Silver" to the wishlist
    Then user should have product "Leprechaun's Silver" in the wishlist
    Then I change my current channel to "Web-US"
    And user has a wishlist in "Web-US"
    Then user should have an empty wishlist


  @api
  Scenario: Adding product to wishlisht on both channels
    Given I change my current channel to "Web-EU"
    And user has a wishlist in "Web-EU"
    When user adds product "Leprechaun's Silver" to the wishlist
    Then user should have product "Leprechaun's Silver" in the wishlist
    Then I change my current channel to "Web-US"
    And user has a wishlist in "Web-US"
    When user adds product "Leprechaun's Gold" to the wishlist
    Then user should have product "Leprechaun's Gold" in the wishlist

  @api
  Scenario: Adding product to wishlist on both channels and removing from one channel.
    Given I change my current channel to "Web-EU"
    And user has a wishlist in "Web-EU"
    When user adds product "Leprechaun's Silver" to the wishlist
    Then user should have product "Leprechaun's Silver" in the wishlist
    Then I change my current channel to "Web-US"
    And user has a wishlist in "Web-US"
    When user adds product "Leprechaun's Gold" to the wishlist
    Then user should have product "Leprechaun's Gold" in the wishlist
    Then user removes product "Leprechaun's Gold" from the wishlist
    Then user should have an empty wishlist
    And I change my current channel to "Web-EU"
    Then user should have product "Leprechaun's Silver" in the wishlist on "Web-EU"
