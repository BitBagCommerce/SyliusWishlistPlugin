@wishlist
Feature: Exporting a product from wishlist to pdf
    In order to save products and buy later
    As a Visitor
    I want to be able to exporting products to pdf

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Removing selected products from wishlist
        Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        And I have this product in my wishlist
        When I go to the wishlist page
        Then I check "Jack Daniels Gentleman"
        And I export to pdf selected products from wishlist and file is downloaded
