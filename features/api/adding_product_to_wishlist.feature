@api_wishlist
Feature: Adding a product to wishlist
    @api
    Scenario: Adding a product to wishlist as an anonymous user
        Given Anonymous user has a wishlist
        And the store has a product "Jack Daniels Gentleman" priced at "$10.00"
        When Anonymous user adds product "Jack Daniels Gentleman" to the wishlist
        Then Anonymous user should have product "Jack Daniels Gentleman" in the wishlist
