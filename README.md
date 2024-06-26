﻿# ugly_code_refactoring

## Overview

This project contains refactored code for calculating commissions based on transaction data. Transactions are read from a file, and commissions are computed according to specified rules.

## Prerequisites

  - PHP >= 8.1
  - Composer

## Installation

1. **Clone the Repository**

      ```bash
      git clone https://github.com/alexkot9111/ugly_code_refactoring.git
      cd ugly_code_refactoring
      ```

2. **Install Dependencies**

  Install all necessary PHP dependencies using Composer. Make sure you have Composer installed globally.
      ```bash
      composer install
      ```
      
## Running the Script

  To run the script that calculates commissions, use the following command:
      ```bash
      php app.php input.txt
      ```

## Running Tests

  To run the PHPUnit tests, use the following command:
      ```bash
      vendor/bin/phpunit --bootstrap vendor/autoload.php tests
      ```
