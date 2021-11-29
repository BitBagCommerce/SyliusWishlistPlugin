@wishlist
Feature: Importing wishlist
  In order to restore my wishlist
  As a Visitor
  I want to be able to import products

  Background:
    Given the store operates on a single channel in "United States"

  @ui
  Scenario: Importing wishlist from csv
    Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And there is 5 units of product "Jack Daniels Gentleman" available in the inventory
    And the product "Jack Daniels Gentleman" is stored in "file.csv"
    And I am on "/wishlist/import/csv"
    When I attach the file "file.csv" to "import_wishlist_from_csv_wishlist_file"
    And I press "import_wishlist_from_csv_submit"
    Then I should be on my wishlist page
    And I should see "Product has been added to your wishlist"
    And I should have "Jack Daniels Gentleman" product in my wishlist

  @ui
  Scenario: Importing wishlist from csv when product variant is already in wishlist
    Given the store has a product "Jack Daniels Gentleman" priced at "$10.00"
    And I have this product in my wishlist
    And the product "Jack Daniels Gentleman" is stored in "file.csv"
    And I am on "/wishlist/import/csv"
    When I attach the file "file.csv" to "import_wishlist_from_csv_wishlist_file"
    And I press "import_wishlist_from_csv_submit"
    Then I should be on my wishlist page
    And I should see "Jack Daniels Gentleman variant is already in wishlist."
    And I should have "Jack Daniels Gentleman" product in my wishlist
