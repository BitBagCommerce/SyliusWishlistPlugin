@wishlist
Feature: Listing wishlists
    In order list all available wishlists
    As a Visitor
    I want to be able to see list of my wishlists

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Listing wishlist
        Given I am on "/"
        And the store has a wishlist named "Wishlist1"
        And the store has a wishlist named "Wishlist2"
        When I go to "/wishlists"
        Then I should have 2 wishlists

    @ui
    Scenario: Listing wishlist as user
        Given there is a customer account "jdeer@sylius.pl"
        And I am logged in as "jdeer@sylius.pl"
        And user "jdeer@sylius.pl" has a wishlist named "Wishlist1" with token "123456token"
        And user "jdeer@sylius.pl" has a wishlist named "Wishlist2" with token "123456token"
        When I go to "/wishlists"
        Then I should have 2 wishlists

    @ui
    Scenario: Listing wishlist as user on same computer as another user
        Given there is a customer account "jdeer@sylius.pl"
        And there is a customer account "user2@sylius.pl"
        And user "jdeer@sylius.pl" has a wishlist named "Wishlist1" with token "123456token"
        And user "jdeer@sylius.pl" has a wishlist named "Wishlist2" with token "123456token"
        And user "user2@sylius.pl" has a wishlist named "Wishlist3" with token "123456token"
        And user "user2@sylius.pl" has a wishlist named "Wishlist4" with token "123456token"
        And user "user2@sylius.pl" has a wishlist named "Wishlist5" with token "123456token"
        And I am logged in as "jdeer@sylius.pl"
        When I go to "/wishlists"
        Then I should have 2 wishlists
