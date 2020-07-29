# User stories

objects:
 - job
    - position
    - type (fulltime / part time / freelance)
    - location
    - company
    - company logo
    - company url
    - description
    - how to apply
    - active (true/false)
    - created at
    - expires at
    - public
    - owner (user)
    - category (category)
 - job category
    - name
    - jobs
 - job owner (user)
    - email
    - jobs
 - admin (user)
    - email
    - password
 - affiliate (user)
    - email
    - website
    - token
    - job category


## docker setup for development

## application skeleton

## template for views

## entities

## setup migrations

## fixtures

## homepage

A list of jobs, latest active 10 jobs, grouped by category. 

## jobs by category

A list of jobs, paginated, from a specific category.

## job search

being able to search jobs by position, type, location, description and category. search results page must be paginated

## job page

show all job properties for those willing to apply. page should have multiple instances: 
 
  - active jobs
  - expired jobs
  - pending approval jobs

## create a job for users

form to create a job. user must be able to upload a company logo.

## edit jobs for owners

form to edit the job

## authentication for job owners through email links

job owners should be able to authenticate if they click an email link available for a brief period of time. 

## job publishing after email confirmation

once a job is submitted, it will be public only after the owner confirms the link received via email.

## extend jobs

owner should be able to extend job, as well as admin.

## multi language

translations for the ui.

## become affiliate

visitors can apply to become affiliates and get access to the api of the project. an affiliate must provide email address 
and a website. login happens through a link sent via email.

affiliates can choose what jobs they can get through api by assigning themselves a category they want access to.

## admin users

the operator of the website should be able to create admin users through CLI. 

## admin interface

users with admin rights should be able to edit all entities.

## purge old inactive jobs

from admin and from cli.

## error page

for 404, 403 and 5xx.

## functional testing

## unit testing

## setup ci

## modern asset management

## create remote infrastructure at digital ocean for production deployment

## setup docker for production deployment

## setup caching for production environment

object caching + http cache + invalidation

## write documentation for the project

## rewrite the repository as a tutorial

Inspired by https://medium.com/@dragosholban/symfony-2-8-jobeet-tutorial-3a72f67cdbd8;

original tutorial: https://symfony.com/blog/jobeet-day-1-starting-up-the-project

revive the project
