# Symfony screenshot websites

- Introduction
  - Tech stack
  - Features
    - Available options
- Installation
  - Requirements
  - Install
- Usage
  - CLI
  - Web
- Caveats
- Notes

## Introduction

A small application to get screenshots from websites, using the API available
at https://docs.screenshotapi.net. To use of this API requires a TOKEN that 
can be retrieved by the user in the 
[Dashboard area](app.screenshotapi.net/dashboard).

### Tech stack

This will be a try out of [Symfony](https://symfony.com), the High Performance
PHP Framework for Web Development. So it will use most of the standards
components available for this framework.

For persisting data, we will use SQLite, just to test things out.


### Features

This will have very few operations:

- Command line command to take the screenshot
- Web interface were we can:
  - Take a screenshot
  - View a list of screenshots taken
  - Display a screenshot

#### Available options

There are a boat load of options, but only some sane options will be made available:

Implemented:
- url (required)
- width { number }
- height { number }
- output { image | json }
- file_type { png | jpeg | webp | pdf }
- lazy_load { boolean }
- dark_mode { boolean }
- grayscale { 0...100}
- delay { number ms }
- user_agent { string }
- full_page { boolean }
- fail_on_error { boolean }
- clip[x] { number }
- clip[y] { number }
- clip[height] { number }
- clip[width] { number }

Not implemented:

- fresh { boolean }
- extract_text { boolean }
- extract_html { boolean }
- block_ads { boolean }
- no_cookie_banners { string }
- retina { boolean }
- destroy_screenshot { boolean}
- block_tracking { boolean }
- omit_background { boolean }
- wait_for_event { load | domcontentloaded | networkidle }
- accept_languages { string }
- selector { string }
- headers { string }
- _there are more_

Note: there is a parameter `token` that will be loaded from the .env file, so
please place it on `API_TOKEN=` entry on the `.env` file.

## Installation

### Requirements

Need php install on the machine and composer.

To install php, follow the guide at [php.net](https://www.php.net/manual/en/install.php) for our OS.

To install composer, follow the guide at [getcomposer.org](https://getcomposer.org/download/).

To install sqlite3, follow the guide at [sqlite.org](https://sqlite.org/download.html). (Note on non-Windows, it's 
usually available already, check with `which sqlite3` before install).

### Install

To install the application:

1. Clone the repository
2. Copy `.env` file: `cp .env.example .env`
3. Prepare `.env` file:
   - Edit `DATABASE_URL=` to point to database
   - Edit `API_TOKEN=` to add your token from https://app.screenshotapi.net/dashboard
4. Run composer to install dependencies: `composer install`
5. Prepare database
   1. Prepare database: `php bin/console doctrine:database:create`
   2. Run existing migrations: `php bin/console doctrine:migrations:migrate`
6. Run server to test: `php -S localhost:8000`
7. Point the browser to: `localhost:8000/public`

## Usage 

The application as two modes, a cli command and web interface.

### CLI

To use the command line, run:

```bash
php bin/console app:sshot <url> [options]
```

Example:

```bash
$ php bin/console app:sshot www.google.pt --file_type jpeg --width 1024 --height 860 --fail_on_error
Taking a screenshot off website www.google.pt
Save screenshot at /Users/bandarra/nifty/work/integrity/sscreenshot/public/screenshots/www.google.pt_20211226152707697062.jpeg (3044 bytes) - 2021-12-26T15:27:07+00:00
```

The images will be store on project folder `public/screenshots`.

### Web

The web component will be small and will only make available the following routes:

- `GET /` - list all screenshots taken
- `GET /screenshot/{id}` - retrieve a json with info about screenshot image, to be used in a modal
- `GET | POST /screenshot/new` - take a new screenshot

## Caveats

Didn't make the app running with Docker but seems possible to container this, creating the following services:
- nginx
- php-symfony
- db

## Notes

Symfony has great documentation and seems more efficient (dependency wise) than Laravel. For a first time symfony project
it was nice.