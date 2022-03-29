@wishlist
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

  @ui
  Scenario: Adding product to wishlist in the first channel checking wishlist on the second channel.
    Given I change my current channel to "Web-EU"
    When I add "Leprechaun's Silver" product to my wishlist
    And I should be notified that the product has been successfully added to my wishlist
    Then I change my current channel to "Web-US"
    And I go to the wishlist page
    And I should have 0 products in my wishlist

  @ui
  Scenario: Adding product to wishlisht on both channels
    Given I change my current channel to "Web-EU"
    When I add "Leprechaun's Silver" product to my wishlist
    And I should be notified that the product has been successfully added to my wishlist
    And I should have one item in my wishlist
    Then I change my current channel to "Web-US"
    And I add "Leprechaun's Gold" product to my wishlist
    And I should be notified that the product has been successfully added to my wishlist
    And I should have one item in my wishlist

  @ui
  Scenario: Adding product to wishlist on both channels and removing from one channel.
    Given I change my current channel to "Web-EU"
    When I add "Leprechaun's Silver" product to my wishlist
    And I should be notified that the product has been successfully added to my wishlist
    Then I change my current channel to "Web-US"
    And I add "Leprechaun's Gold" product to my wishlist
    And I should be notified that the product has been successfully added to my wishlist
    And I should have one item in my wishlist
    And I go to the wishlist page
    Then I check "Leprechaun's Gold"
    And I remove selected products from wishlist
    And I should have 0 products in my wishlist
    And I change my current channel to "Web-EU"
    And I go to the wishlist page
    And I should have one item in my wishlist

  @ui
  Scenario: Adding multiple channels product to wishlist and removing one.
    Given I change my current channel to "Web-EU"
    When I add "Leprechaun's Gold" product to my wishlist
    And I should be notified that the product has been successfully added to my wishlist
    Then I change my current channel to "Web-US"
    And I add "Leprechaun's Gold" product to my wishlist
    And I should be notified that the product has been successfully added to my wishlist
    And I should have one item in my wishlist
    And I go to the wishlist page
    Then I check "Leprechaun's Gold"
    And I remove selected products from wishlist
    And I should have 0 products in my wishlist
    And I change my current channel to "Web-EU"
    And I go to the wishlist page
    And I should have one item in my wishlist

