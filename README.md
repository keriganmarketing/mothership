# KMA MLS Mothership

## An API for our BCAR and ECAR real estate sites

## Build Status
[![Build Status](https://travis-ci.org/Doomtickle/mothership.svg?branch=master)](https://travis-ci.org/Doomtickle/mothership)

CPAR / ECAR Mothership API
## Endpoints
All endpoints for the public facing API share the same prefix of
`https://mothership.kerigan.com/api/v1/`
   * /search
	   * The main endpoint for performing real estate searches
       * Returns an [Eloquent Collection](https://laravel.com/docs/5.7/collections) of search results
	   * **GET Variables** (all variables are optional)
		   * *city* - (String)
		   * *status* - (String)
		   * *propertyType* - (String)
		   * *openHouses* - (Boolean)
			   * Returns only listings with openHouses
		   * *minPrice* - (Integer)
           * *maxPrice* - (Integer)
           * *beds* - (Integer)
           * *baths* - (Integer)
           * *sqft* - (Integer)
           * *acreage* - (Integer)
           * *waterfront* - (Boolean)
           * *pool* - (Boolean)
           * *sortBy* - (String)
                * Column in the listings table used to sort returned results
           * *orderBy* - (String)
                * `ASC` or `DESC`
        * listing/{mlsNumber}/
            * Returns a single listing with all of its relationships.
        * listings/
            * Returns a collection of listings matching the given MLS numbers
            * **GET Variables**
                * *mlsNumbers* - (String)
                    * A strongbar delimited list of MLS numbers for which you wish to see results
                    Example:
                        `https://mothership.kerigan.com/api/v1/listings?mlsNumbers=12345|12346|12347`
        * omnibar/
            * Receives user input from client and searches database for columns matching input
            * **GET Variables**
                * *searchTerm* - (String)
        * allMapListings/
            * Return an unpaginated collection of listing matching the given search criteria
            * **GET Variables**
                * see search variables
        * agents/
            * Returns agent details based on search criteria
            * **GET Variables**
                * *shortId* - (String)
                * *fullName* - (String)
                * *lastName* - (String)
                * *firstName* - (String)
                * *association* - (String)
                * *officeShortId* - (String)

## Maintaining the Application

> Note: BCAR has changed it's name to CPAR. However, BCAR is still used throughout the application and this manual to refer to CPAR.

The app is a self-sustaining clone of the MLS databases with a user-facing JSON API. Ideally, little to no interaction should be necessary once things are working properly. Below is a brief tour of the application:

### The ENV file
Inside the `.env` file, you will need to specify your connections to the MLS database using
```
BCAR_USERNAME={your_bcar_username}
BCAR_PASSWORD={your_bcar}

ECAR_USERNAME={your_ecar_username}
ECAR_PASSWORD={your_ecar_password}
```

### App\Helpers Folder
Inside this folder is where a lot of the "normalization" of the separate data feeds happen.

* *BcarOptions/EcarOptions Classes*
    * These classes specifiy the specific columns requested from their respective associations. The columns listed are what the master `listings` table needs to be complete.

* *Builder.php*
    * This class contains all of the functions needed to build a fresh set of data for the association. To run the builder, simply use the following command from inside the `php artisan tinker` console:

    * BCAR example
        ```php
        (new Builder('bcar'))->rebuild();
        ```

    * ECAR example
        ```php
        (new Builder('ecar'))->rebuild();
        ```
    The `rebuild()` method will run all the necessary functions to build a fresh set of data in the database.
* **App\Updaters Folder**
    * The classes in this folder are responsible for running the updaters on listings, agents, openhouses, etc.
    * There are also helper methods included to force updates for a certain class, update a single listing, get photos for a specific listing, and more.
    * To instantiate the class and manually force updates, just use the same syntax as you would for the `Builder.php` class in your `php artisan tinker` console.
        ```php
        $updater = new ListingsUpdater('bcar');
        $updater->update();
        ```
* **Queues and Jobs**
    * The application updates continuously to ensure fresh data is always available. This is handled through [Queues](https://laravel.com/docs/5.5/queues), [Jobs](https://laravel.com/docs/5.5/queues#generating-job-classes), and [Tasks](https://laravel.com/docs/5.5/scheduling).
    * The main queue engine uses REDIS to manage the queues and a dashboard to monitor these jobs is located at [https://mothership.kerigan.com/horizon](https://mothership.kerigan.com/horizon)
* **Server Configuration**
    * Care was taken to make the server setup process as painless as possible, but there are a few custom configurations that we'll have to include.
    * While editing the site in the Forge dashboard, you'll need to set up a queue as follows:

    ![forge dashboard](https://github.com/keriganmarketing/mothership/blob/master/forge_dashboard.png)

    * Additionally, add `php artisan horizon:terminate` to the end of your deploy script like so:

    ![deploy script](https://github.com/keriganmarketing/mothership/blob/master/deploy_script.png)

