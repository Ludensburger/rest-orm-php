<?php
class UserRepository implements DataRepositoryInterface {
    private PDO $db;
    private string $tableName = '';
    private string $query = '';
    private array $valueBag = [];

    public function __construct(string $host, string $user, string $password, string $dbname) {
        try {
            $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ];
            $this->db = new PDO($dsn, $user, $password, $options);
        } catch (PDOException $e) {
            die("Database connection failed in UserRepository: " . $e->getMessage());
        }
    }

    public function table(string $tableName): object {
        $this->tableName = $tableName;
        return $this;
    }

    public function insert(array $values): int {
        $columns = implode(', ', array_keys($values));
        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $this->query = "INSERT INTO $this->tableName ($columns) VALUES ($placeholders)";
        $this->valueBag = array_values($values);

        $stmt = $this->db->prepare($this->query);
        $stmt->execute($this->valueBag);
        return $stmt->rowCount();
    }

    public function get(): array {
        // Debugging statements
        // echo "Query: " . $this->query . "<br>"; // Debug statement
        // echo "Value Bag: " . print_r($this->valueBag, true) . "<br>"; // Debug statement
        $stmt = $this->db->prepare($this->query);
        $stmt->execute($this->valueBag);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result : [];
    }

    public function getAll(): array {
        $this->query = "SELECT * FROM $this->tableName";
        $stmt = $this->db->prepare($this->query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function select(array $fieldList = null): object {
        $fields = $fieldList ? implode(', ', $fieldList) : '*';
        $this->query = "SELECT $fields FROM $this->tableName";
        return $this;
    }

    public function from($table): object {
        $this->tableName = $table;
        $this->query = "SELECT * FROM $table";
        return $this;
    }

    public function where($field, $value, $operator = '='): object {
        if (strpos($this->query, 'WHERE') === false) {
            $this->query .= " WHERE $field $operator ?";
        } else {
            $this->query .= " AND $field $operator ?";
        }
        $this->valueBag[] = $value;
        return $this;
    }
    
    public function whereOr(): object {
        $this->query .= ' OR ';
        return $this;
    }

    public function showQuery(): string {
        return $this->query;
    }

    public function update(array $values): int {
        $setClauses = [];
        $valuesToBind = [];
        foreach ($values as $column => $value) {
            $setClauses []= "$column = ?";
            $valuesToBind []= $value;
        }
        $setClause = implode(', ', $setClauses);
    
        $sql = "UPDATE $this->tableName SET $setClause";
    
        // Append the WHERE clause from the query if it exists
        if (strpos(strtoupper($this->query), 'WHERE') !== false) {
            $sql .= substr($this->query, strpos(strtoupper($this->query), 'WHERE'));
        }
    
        $stmt = $this->db->prepare($sql);
    
        // Merge the values for the SET clause and the WHERE clause
        $paramsToExecute = array_merge($valuesToBind, $this->valueBag);
    
        // Bind the parameters
        for ($i = 1; $i <= count($paramsToExecute); $i++) {
            $stmt->bindValue($i, $paramsToExecute[$i - 1]);
        }
    
        $stmt->execute();
        return $stmt->rowCount();
    }

    public function delete(): int {
        $this->query = "DELETE FROM $this->tableName" . $this->query;
        $stmt = $this->db->prepare($this->query);

        // Bind the parameters
        for ($i = 1; $i <= count($this->valueBag); $i++) {
            $stmt->bindValue($i, $this->valueBag[$i - 1]);
        }

        $stmt->execute();
        return $stmt->rowCount();
    }

    public function showValueBag(): array {
        return $this->valueBag;
    }

    // public function getAll() {
    //     return $this->db->query("SELECT * FROM users");
    // }

    // public function getById($id) {
    //     return $this->db->query("SELECT * FROM users WHERE id = ?", [$id]);
    // }

    // public function create($data) {
    //     $this->db->query("INSERT INTO users (id, name, email) VALUES (?, ?, ?)", [$data['id'], $data['name'], $data['email']]);
    // }

    // public function update($id, $data) {
    //     $this->db->query("UPDATE users SET name = ?, email = ? WHERE id = ?", [$data['name'], $data['email'], $id]);
    // }

    // public function delete($id) {
    //     $this->db->query("DELETE FROM users WHERE id = ?", [$id]);
    // }
}