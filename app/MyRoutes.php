<?php

namespace App;

use BasicAPI\Exception\BadRequestException;
use BasicAPI\Exception\ForbiddenException;
use BasicAPI\Exception\NotFoundException;
use BasicAPI\Request;
use BasicAPI\Router;
use NoahBuscher\Macaw\Macaw;

class MyRoutes extends Router
{
    public function setRoutingRules()
    {
        Macaw::haltOnMatch(true);
        Macaw::get(
            '/categories/(:num)',
            function ($category_id) {
                $data = $this->db->getCategory($category_id);
                if ($data !== false) {
                    $this->response->showResponse('', 200, $data);
                } else {
                    throw new NotFoundException('No data found');
                }
            }
        );

        Macaw::head(
            '/categories/(:num)',
            function () {
                $this->response->showResponse('', 200);
            }
        );

        Macaw::get(
            '/categories',
            function () {
                $data = $this->db->getAllCategories();
                if ($data !== false) {
                    $this->response->showResponse('', 200, $data);
                } else {
                    throw new NotFoundException('No data found');
                }
            }
        );

        Macaw::head(
            '/categories',
            function () {
                $this->response->showResponse('', 200);
            }
        );

        Macaw::get(
            '/categories/(:num)/products',
            function ($category_id) {
                $data = $this->db->getAllProductsFromCategory($category_id);
                if ($data !== false) {
                    $this->response->showResponse('', 200, $data);
                } else {
                    throw new NotFoundException('No data found');
                }
            }
        );

        Macaw::head(
            '/categories/(:num)/products',
            function () {
                $this->response->showResponse('', 200);
            }
        );

        Macaw::get(
            '/products',
            function () {
                $data = $this->db->getAllProducts();
                if ($data !== false) {
                    $this->response->showResponse('', 200, $data);
                } else {
                    throw new NotFoundException('No data found');
                }
            }
        );

        Macaw::head(
            '/products',
            function () {
                $this->response->showResponse('', 200);
            }
        );

        Macaw::delete(
            '/categories/(:num)',
            function ($category_id) {
                if ($this->access->allowOnlyForAuthorizedUsers($this->db)) {
                    $data = $this->db->deleteCategory($category_id);
                    if ($data === null) {
                        throw new ForbiddenException('You can\'t delete category with products in it, delete all products first');
                    } elseif ($data === false) {
                        throw new NotFoundException('No data found');
                    } else {
                        $this->response->showResponse('Deleted', 200);
                    }
                }
            }

        );

        Macaw::post(
            '/categories',
            function () {
                if ($this->access->allowOnlyForAuthorizedUsers($this->db)) {
                    $request = new Request();
                    $request_payload = $request->getParsedBody();
                    if (is_array($request_payload)) {
                        $last_id = $this->db->createCategory($request->getParsedBody());
                        if (is_int($last_id)) {
                            $this->response->showResponse('Created', 200, ['id' => $last_id]);
                        } else {
                            throw new NotFoundException('Error when creating category, check your payload.');
                        }
                    } else {
                        throw new BadRequestException('Invalid request payload');
                    }
                }
            }
        );

        Macaw::put(
            '/categories',
            function () {
                if ($this->access->allowOnlyForAuthorizedUsers($this->db)) {
                    $request = new Request();
                    $request_payload = $request->getParsedBody();
                    if (is_array($request_payload)) {
                        if ($this->db->updateCategory($request->getParsedBody()) === true) {
                            $this->response->showResponse('Updated', 200);
                        } else {
                            throw new NotFoundException('Error when updating category, check your payload.');
                        }
                    } else {
                        $this->response->showResponse('Invalid request payload', 400);
                    }
                }
            }
        );

        Macaw::delete(
            '/products/(:num)',
            function ($product_id) {
                if ($this->access->allowOnlyForAuthorizedUsers($this->db)) {
                    $data = $this->db->deleteProduct($product_id);
                    if ($data !== false) {
                        $this->response->showResponse('Deleted', 200);
                    } else {
                        throw new NotFoundException('No data found');
                    }
                }
            }
        );

        Macaw::post(
            '/products',
            function () {
                if ($this->access->allowOnlyForAuthorizedUsers($this->db)) {
                    $request = new Request();
                    $request_payload = $request->getParsedBody();
                    if (is_array($request_payload)) {
                        $last_id = $this->db->createProduct($request->getParsedBody());
                        if (is_int($last_id)) {
                            $this->response->showResponse('Created', 200, ['id' => $last_id]);
                        } else {
                            throw new NotFoundException('Error when creating product, check your payload.');
                        }
                    } else {
                        throw new BadRequestException('Invalid request payload');
                    }
                }
            }
        );

        Macaw::put(
            '/products',
            function () {
                if ($this->access->allowOnlyForAuthorizedUsers($this->db)) {
                    $request = new Request();
                    $request_payload = $request->getParsedBody();
                    if (is_array($request_payload)) {
                        if ($this->db->updateProduct($request->getParsedBody()) === true) {
                            $this->response->showResponse('Updated', 200);
                        } else {
                            throw new NotFoundException('Error when updating product, check your payload.');
                        }
                    } else {
                        $this->response->showResponse('Invalid request payload', 400);
                    }
                }
            }
        );

        Macaw::error(
            function () {
                $this->response->showResponse("Routing error", 404);
            }
        );
    }
}