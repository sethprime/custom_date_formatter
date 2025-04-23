# Vedic Date Formatter

The Vedic Date Formatter module extends Drupal's date formatting capabilities by adding support for Vedic time concepts, specifically Muhūrtas.

## Overview

This module provides a customizable PHP date formatting character that allows site builders to incorporate Vedic time divisions (Muhūrta) into their date formats. Muhūrtas sare 48-minute time divisions used in Vedic timekeeping, with 30 Muhurtas making up a full day.

For a full description of the module, visit the
[project page](https://www.drupal.org/project/date_formatter_vedic).

Submit bug reports and feature suggestions, or track changes in the
[issue queue](https://www.drupal.org/project/issues/date_formatter_vedic)


## Requirements

- Drupal 11.x
- PHP 8.1 or higher

## Installation

### Using Composer (recommended)

```
composer require drupal/vedic_date_formatter
```

### Manual Installation

1. Download the module and place it in your site's `modules/contrib` directory
2. Enable the module via the Extend page (`/admin/modules`) or using Drush:

   ```
   drush en vedic_date_formatter
   ```

## Configuration

The date format character can be set, and the muhūrta names overriden.

## Usage

### Date Format Character

After enabling the module, you can use the format character, defaults to `q`, in your date format patterns:

- `q` - The current Muhurta name (e.g., "Rudra", "Ahir Budhnya")

### Examples

You can customize your date formats at **Administration » Configuration » Regional and language » Vedic Date Formatter**.

Example date format string:

```
jS F Y, g:i a (Muhurta: q)
```

This would display as:

```
23rd April 2025, 10:30 am (Muhurta: Surya)
```
