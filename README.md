## About this repository

This repository demonstrates how to build a rudimentary API with the Joomla Framework, using a "standard" model, view, controller pattern with full Dependency Injection using the Joomla Framework's DI Container

### Open API
The [Open API Initiative (OAI)](https://openapis.org/) was created by a consortium of forward-looking industry experts who recognize the immense value of standardizing on how REST APIs are described 

This repository uses the Open API initiative to generate documentation of the endpoints and also for use as a routing file.

### The Joomla Framework

The Joomla Framework was founded in 2013. This package largely uses various packages of version 1.0 of the framework. It uses the development version 2.0 package for the views as these give much better non-HTML support.

### Presentations
This repository was demonstrated as part of J And Beyond 2016 in Barcelona

### How to run
This repository relies on composer. To ensure you have the latest versions of the Joomla Framework, Symfony etc libraries, simply run `composer update` from the root of this package.

The main entry point to this API is `www/index.php`. The `www` directory is the only directory that is required to be in the web root. All other libraries can be held outside your web root.
