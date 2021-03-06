# Phactory: PHP Database Object Factory for Unit Testing

[![Build Status](https://travis-ci.org/BaguettePHP/phactory.svg)](https://travis-ci.org/BaguettePHP/phactory)
[![Package version](http://img.shields.io/packagist/v/zonuexe/phactory.svg)](https://packagist.org/packages/zonuexe/objectsystem)
[![Apache License 2.0](https://img.shields.io/github/license/BaguettePHP/phactory.svg)](https://spdx.org/licenses/Apache-2.0)

## What is it?

Phactory is an alternative to using database fixtures in your PHP unit tests.
Instead of maintaining a separate XML file of data, you define a blueprint
for each table, and then create as many different objects as you need.

Phactory was inspired by Factory Girl.

## Features

* Define default values for your table rows once with Phactory::define(),
then easily create objects in that table with a call to Phactory::create().
* Create associations between your defined tables, and the objects will automatically
be associated in the database upon creation.
* Use sequences to create unique values for each successive object you create.

## Database Support

* MySQL
* Sqlite
* Postgresql

## Language Support

* PHP >= 5.3

## Limitations

* Each table must have a single integer primary key for associations to work.
