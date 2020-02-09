# Task description
You need to write the simplest API for the product catalog.

### The application should contain:
  - Product Categories;
  - Specific products that belong to a certain category (one product may belong to
several categories);
  - Users who can log in.

### Possible actions:
  - List all categories;
  - Getting a list of products in a specific category;
  - User Authorization;
  - Adding / Editing / Deleting a category (for authorized users);
  - Add / Edit / Delete product (for authorized users).
  
-----------------------------------------------------
# What were done

### General API Features

- Bearer Authorization (tokens must be valid and not expired)
- SQLite DB
- Multiple configs (you can load any config by passing GET parameter `config=filenamewithoutextension`, `local.php` will be loaded by default)
- Registry (set/get)

See [MyRoutes.php](!https://github.com/madlopt/BasicAPI/blob/master/request_samples_local.http) to review all available routes and see how it works. The API tries to catch any possible error and give appropriate response.

### Database structure

`api_product_categories`
- id
- name

`api_products`
- id
- name
- category_id

`api_users`
- id
- name
- token 
- token_expiration_time (UNIX time)

### Tips

Please check [request_samples_local.http](!https://github.com/madlopt/BasicAPI/blob/master/request_samples_local.http) file inside your PHPStorm to run all request samples.
The repository is there https://github.com/madlopt/BasicAPI

### Requirements

- PHP 7.4 or newer
- sqlite3 extension
- R/W rights on directories: `var/logs`, `var/db` or on `var` and all sub-directories.
- Apache Web Server with mod_rewrite. If you do not have Apache, you need to set rewrite rule: all requests for API's directory must be rewritten to `public/index.php`.

### API Methods
- GET /categories/

Get all of categories

- GET /categories/{id}

Get a specific category

- GET /categories/{id}/products

Get all products within specific category

- GET /products/ 

Get all of products

```I've made all HEAD requests according to each GET request for browser's pre-flight requests```

- POST /categories/

Returns last insert id. Example of payload: `{"name":"text"}`
- DELETE /categories/{id}

No payload needed.

- PUT /categories/ 

 Example of payload: `{"id":10,"name":"text"}`
- POST /products

Returns last insert id. Example of payload: `{"name":"lalalala5","category_id":2}`

- DELETE /products/{id}

No payload needed.

- PUT /products

Example of payload: `{"id":10,"name":"lalalala5","category_id":2}`
