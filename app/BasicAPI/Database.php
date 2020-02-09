<?php

namespace BasicAPI;

use SQLite3;

class Database
{
    private SQLite3 $db_connection;

    public function __construct(SQLite3 $db = null)
    {
        if (empty($db)) {
            $this->db_connection = Registry::get('db_connection');
        } else {
            $this->db_connection = $db;
        }
    }

    public function getTokenData($token)
    {
        $stmt = $this->db_connection->prepare("SELECT * FROM api_users WHERE token = :token");
        $stmt->bindParam(':token', $token, SQLITE3_TEXT);
        $res = $stmt->execute();
        return $res->fetchArray(SQLITE3_ASSOC);
    }

    public function deleteProduct($id)
    {
        $id = (int)$id;
        return $this->makeQuery("DELETE FROM api_products WHERE id = $id");
    }

    private function makeQuery($query)
    {
        $result = $this->db_connection->query($query);

        $last_error_message = $this->db_connection->lastErrorMsg();
        if ($last_error_message !== 'not an error') {
            $logger = Registry::get('logger');
            $logger->error(json_encode($last_error_message));
            return false;
        }

        if (strtoupper(substr($query, 0, 6)) === 'DELETE' OR strtoupper(substr($query, 0, 6)) === 'UPDATE') {
            if ($this->db_connection->changes() > 0) {
                return true;
            } else {
                return false;
            }
        }

        if ($result !== false) {
            $array = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $array[] = $row;
            }
            if (empty($array)) {
                return false;
            } else {
                return $array;
            }
        } else {
            return false;
        }
    }

    public function deleteCategory($id)
    {
        $id = (int)$id;
        $result = $this->makeQuery("SELECT 1 FROM api_products WHERE category_id = $id");
        if ($result === false) {
            return $this->makeQuery("DELETE FROM api_product_categories WHERE id = $id");
        } else {
            return null;
        }
    }

    public function updateCategory(array $data)
    {
        if (empty($data['id']) OR empty($data['name'])) {
            return false;
        }

        $stmt = $this->db_connection->prepare("UPDATE api_product_categories SET name = :name WHERE id = :id ");
        $stmt->bindParam(':name', $data['name'], SQLITE3_TEXT);
        $stmt->bindParam(':id', $data['id'], SQLITE3_INTEGER);
        $stmt->execute();
        if ($this->db_connection->changes() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function updateProduct(array $data)
    {
        if (empty($data['id']) OR empty($data['name']) OR empty($data['category_id'])) {
            return false;
        }

        $stmt = $this->db_connection->prepare(
            "UPDATE api_products SET name = :name, category_id = :category_id WHERE id = :id "
        );
        $stmt->bindParam(':name', $data['name'], SQLITE3_TEXT);
        $stmt->bindParam(':category_id', $data['category_id'], SQLITE3_INTEGER);
        $stmt->bindParam(':id', $data['id'], SQLITE3_INTEGER);
        $stmt->execute();
        if ($this->db_connection->changes() > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function createCategory(array $data)
    {
        if (empty($data['name'])) {
            return false;
        }

        $stmt = $this->db_connection->prepare("INSERT INTO api_product_categories VALUES (null, :name)");
        $stmt->bindParam(':name', $data['name'], SQLITE3_TEXT);
        $stmt->execute();
        $last_id = $this->db_connection->lastInsertRowID();
        if (!empty($last_id)) {
            return $last_id;
        } else {
            return false;
        }
    }

    public function createProduct(array $data)
    {
        if (empty($data['name']) OR empty($data['category_id'])) {
            return false;
        }

        $stmt = $this->db_connection->prepare("INSERT INTO api_products VALUES (null, :name, :category_id)");
        $stmt->bindParam(':category_id', $data['category_id'], SQLITE3_INTEGER);
        $stmt->bindParam(':name', $data['name'], SQLITE3_TEXT);
        $stmt->execute();
        $last_id = $this->db_connection->lastInsertRowID();
        if (!empty($last_id)) {
            return $last_id;
        } else {
            return false;
        }
    }

    public function getAllCategories()
    {
        return $this->makeQuery('SELECT * FROM api_product_categories');
    }

    public function getAllProducts()
    {
        return $this->makeQuery('SELECT * FROM api_products');
    }

    public function getAllProductsFromCategory($category_id)
    {
        $category_id = (int)$category_id;
        return $this->makeQuery("SELECT * FROM api_products WHERE category_id = $category_id");
    }

    public function getCategory($category_id)
    {
        $category_id = (int)$category_id;
        return $this->makeQuery("SELECT * FROM api_product_categories WHERE id = $category_id");
    }
}