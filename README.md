# SEO Analyzer

Basic PHP library to check several SEO metrics of a website.

[![Travis](https://img.shields.io/travis/grgk/seo-analyzer.svg)](https://travis-ci.org/grgk/seo-analyzer)
[![Packagist](https://img.shields.io/packagist/v/grgk/seo-analyzer.svg)](https://github.com/grgk/seo-analyzer)
[![Quality Gate](https://sonarcloud.io/api/project_badges/measure?project=grgk.seo-analyzer&metric=alert_status)](https://sonarqube.com/dashboard/index/grgk.seo-analyzer)
[![SonarQube Coverage](https://sonarcloud.io/api/project_badges/measure?project=grgk.seo-analyzer&metric=coverage)](https://sonarqube.com/dashboard/index?id=grgk.seo-analyzer)
[![license](https://img.shields.io/github/license/mashape/apistatus.svg)](http://opensource.org/licenses/MIT)
[![GitHub issues](https://img.shields.io/github/issues/grgk/seo-analyzer.svg)](https://github.com/grgk/seo-analyzer/issues?q=is%3Aopen)
[![StyleCI](https://styleci.io/repos/161350613/shield?branch=master)](https://styleci.io/repos/161350613)
[![Code Climate](https://codeclimate.com/github/grgk/seo-analyzer/badges/gpa.svg)](https://codeclimate.com/github/grgk/seo-analyzer)

## Requirements
* PHP 7.1 (or higher)

## Installation

composer require grgk/seo-analyzer

## How to use?

Please check the `examples` dir

## Available metrics

This tools uses several metrics to analyze and rate website suggesting some SEO improvements:

* https - checks for SSL encryption,
* redirect - checks URL for redirects,
* page_size - analyzes page size,
* load_time - analyzes page load time,
* url_size - analyzes URl size,
* meta - analyzes page meta tags,
* headers - analyzes page HTML headers,
* keyword_density - analyzes keyword density in page content,
* keyword_density_headers - analyzes keyword density in HTML headers on page,
* alt_attributes - analyzes images alt attributes content,
* keyword_url - analyzes URL for specified SEO keyword,
* keyword_path - analyzes URL: path for specified keyword,
* keyword_title - analyzes page title for specified keyword,
* keyword_description - analyzes page meta description for keyword,
* keyword_headers - analyzes HTML headers for keyword,
* keyword_density_keyword - analyzes page content keyword density for specified keyword,
* robots - analyzes "robots.txt" file
* sitemap - analyzes "sitemap.xml" file

## Translations

On how to translate messages please check `examples/analyze_url_with_keyword_translated.php` file.

You can add more translations by adding translation files to `locale` directory.

## License
Licensed under the [MIT license](http://opensource.org/licenses/MIT).