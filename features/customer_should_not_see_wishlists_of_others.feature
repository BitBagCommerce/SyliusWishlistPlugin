@wishlist
Feature: Wishlists functionality for customers
    As a Customer
    I want to see only my wishlists

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer account "klaus@sylius.pl"
        And user "klaus@sylius.pl" has a wishlist named "Wishlist" with token "123456"
        And there is a customer account "monica@sylius.pl"

    @ui
    Scenario: Should only my wishlists
        Given I log in as "klaus@sylius.pl"
        And I go to "/wishlists"
        And I should have 1 wishlists
        When I log out
        And I log in as "monica@sylius.pl"
        And I go to "/wishlists"
        Then I should have 0 wishlists
        