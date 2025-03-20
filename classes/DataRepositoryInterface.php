<?php
interface DataRepositoryInterface {
    // public function getAll();
    // public function getById($id);
    // public function create($data);
    // public function update($id, $data);
    // public function delete($id);

    public function table(string $tableName): object;
    public function insert(Array $values): int;
    public function get(): array;
    public function getAll(): array;
    public function select(Array $fieldList=null): object;
    public function from($table): object;
    public function where($field, $value, $operator = '='): object; // Updated line
    public function whereOr(): object;
    public function showQuery(): string;
    public function update(Array $values): int;
    public function delete(): int;
    public function showValueBag(): array;
}

