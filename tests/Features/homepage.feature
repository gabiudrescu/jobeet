Feature:
  In order to promote our jobs
  As a product owner
  I want to display latest 4 from all categories

  Background:
    Given fixtures were generated

  Scenario: I should see categories with active jobs on the homepage
    Given I am on homepage
    Then I should see 4 "h1.category" elements
