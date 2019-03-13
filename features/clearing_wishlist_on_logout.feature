@wishlist
Feature: Clearing wishlist on logout
    In order to not see other users wishlists
    As a Visitor
    I want to have cleared wishlist after logout

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Clearing wishlist on logout
        Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And I have this product in my wishlist
        When I log in
        And I log out
        And I go to the wishlist page
        And I should have 0 products in my wishlist
