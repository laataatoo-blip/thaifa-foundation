<?php
// https://github.com/navysingchai/program_documentation

if (!class_exists('Database')) {
    include_once(__DIR__ . '/Database.class.php');
}
class DatabaseManagement extends Database
{

    private function escapeValue($value)
    {
        if (is_null($value)) {
            return 'NULL';
        } elseif (is_int($value) || is_float($value)) {
            return $value;
        } else {
            return "'" . str_replace("'", "''", $value) . "'";
        }
    }

    public function clearTransaction()
    {
        $this->dbConn = null;
    }

    public function insert($tbl, $param = [])
    {
        try {
            $this->dbConn->beginTransaction();
            $cols = implode(', ', array_keys($param));
            $placeholders = ':' . implode(', :', array_keys($param));
            $sql = "INSERT INTO $tbl ($cols) VALUES ($placeholders)";
            $stmt = $this->dbConn->prepare($sql);

            foreach ($param as $col => $val) {
                $val = isset($val) && !is_null($val) ? trim($val) : "";
                $val = !empty($val) ? $val : null;
                if (gettype($val) == "integer") {
                    $stmt->bindValue(":$col", $val, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue(":$col", $val);
                }
            }

            if ($stmt->execute()) {
                if ($this->dbConn->lastInsertId() > 0 || $stmt->rowCount() > 0) {
                    $LastInsertID = $this->dbConn->lastInsertId();
                    $r = [
                        'status' => 'success',
                        'type' => 'insert',
                        'lastInsertID' => $LastInsertID,
                        'LastInsertID' => $LastInsertID
                    ];
                    $this->dbConn->commit();
                    return $r;
                } else {
                    $this->dbConn->rollBack();
                    echo "<pre>";
                    print_r([
                        'status' => 'error',
                        'errType' => 'insert',
                        'type' => 'insert',
                        'msgErr' => 'No rows inserted'
                    ]);
                    exit;
                }
            } else {
                $this->dbConn->rollBack();
                echo "<pre>";
                print_r([
                    'status' => 'error',
                    'errType' => 'execute',
                    'type' => 'execute',
                    'msgErr' => 'No rows inserted'
                ]);
                exit;
            }
        } catch (PDOException $e) {
            $this->dbConn->rollBack();
            echo "<pre>";
            print_r([
                'status' => 'error',
                'errType' => 'tryCatch',
                'type' => 'tryCatch',
                'msgErr' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function insertSQL($tbl, $param = [])
    {
        if (empty($param)) {
            return "INSERT INTO `$tbl` DEFAULT VALUES;";
        }

        $columns = implode(", ", array_keys($param));
        $values = [];
        foreach ($param as $key => $value) {
            $values[] = $this->escapeValue($value);
        }
        $valuesStr = implode(", ", $values);

        $sql = "INSERT INTO `$tbl` ($columns) VALUES ($valuesStr);";
        return $sql;
    }


    public function update($table, $bindParam = [], $param_where_condition = [])
    {
        try {
            $this->dbConn->beginTransaction();

            $setClause = [];
            foreach ($bindParam as $key => $val) {
                $setClause[] = "`$key` = :$key";
            }
            $setClauseStr = implode(", ", $setClause);

            $whereClause = [];
            foreach ($param_where_condition as $key => $val) {
                $whereClause[] = "`$key` = :$key";
            }
            $whereClauseStr = implode(" AND ", $whereClause);

            $sql = "UPDATE `$table` SET $setClauseStr";
            if (!empty($whereClauseStr)) {
                $sql .= " WHERE $whereClauseStr";
            }

            $stmt = $this->dbConn->prepare($sql);

            if (is_array($bindParam) && count($bindParam) > 0) {
                foreach ($bindParam as $key => $val) {
                    $val = isset($val) && !is_null($val) ? trim($val) : "";
                    $val = !empty($val) ? $val : null;
                    if (gettype($val) == "integer") {
                        $stmt->bindValue("$key", $val, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue("$key", $val);
                    }
                }
            }

            if (is_array($param_where_condition) && count($param_where_condition) > 0) {
                foreach ($param_where_condition as $key => $val) {
                    $val = isset($val) && !is_null($val) ? trim($val) : "";
                    $val = !empty($val) ? $val : null;
                    if (gettype($val) == "integer") {
                        $stmt->bindValue("$key", $val, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue("$key", $val);
                    }
                }
            }

            $stmt->execute();
            $this->dbConn->commit();
            $r = [
                'status' => 'success'
            ];
            return $r;
        } catch (PDOException $e) {
            $this->dbConn->rollBack();
            echo "<pre>";
            $r = [
                'status' => 'error',
                'errType' => 'updateBindParam',
                'type' => 'tryCatch',
                'error' => $e->getMessage()
            ];
            print_r($r);
            exit;
        }
    }

    public function update_SQL($table, $bindParam = [], $param_where_condition = [])
    {
        try {

            $setClause = [];
            foreach ($bindParam as $key => $val) {
                $setClause[] = "`$key` = :$key";
            }
            $setClauseStr = implode(", ", $setClause);

            $whereClause = [];
            foreach ($param_where_condition as $key => $val) {
                $whereClause[] = "`$key` = :$key";
            }
            $whereClauseStr = implode(" AND ", $whereClause);

            $sql = "UPDATE `$table` SET $setClauseStr";
            if (!empty($whereClauseStr)) {
                $sql .= " WHERE $whereClauseStr";
            }

            return $sql;
        } catch (PDOException $e) {
            $this->dbConn->rollBack();
            echo "<pre>";
            $r = [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
            print_r($r);
            exit;
        }
    }

    public function selectAll($sql, $bindParam = [])
    {
        try {
            $stmt = $this->db()->prepare($sql);
            if (is_array($bindParam) && count($bindParam) > 0) {
                foreach ($bindParam as $key => $val) {
                    if (gettype($val) == "integer") {
                        $stmt->bindValue($key, $val, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue($key, $val, PDO::PARAM_STR);
                    }
                }
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<pre>";
            print_r([
                'status' => 'error',
                'type' => 'tryCatch',
                'sql' => $sql,
                'msgErr' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function selectOne($sql, $bindParam = [])
    {
        try {
            $stmt = $this->db()->prepare($sql);
            if (is_array($bindParam) && count($bindParam) > 0) {
                foreach ($bindParam as $key => $val) {
                    $val = $val !== null ? htmlspecialchars(trim($val), ENT_QUOTES, "UTF-8") : null;
                    if (gettype($val) == "integer") {
                        $stmt->bindValue($key, $val, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue($key, $val, PDO::PARAM_STR);
                    }
                }
            }
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "<pre>";
            print_r([
                'status' => 'error',
                'type' => 'tryCatch',
                'sql' => $sql,
                'msgErr' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function selectSQL($sql, $bindParam = [])
    {
        foreach ($bindParam as $key => $value) {
            $quoted = is_numeric($value) ? $value : "'" . addslashes($value) . "'";
            $sql = preg_replace('/' . $key . '\b/', $quoted, $sql);
        }
        print_r($sql);
        exit;
    }

    public function query($sql, $bindParam = [])
    {
        try {
            $stmt = $this->db()->prepare($sql);
            if (is_array($bindParam) && count($bindParam) > 0) {
                foreach ($bindParam as $key => $val) {
                    $val = $val !== null ? htmlspecialchars(trim($val), ENT_QUOTES, "UTF-8") : null;
                    if (gettype($val) == "integer") {
                        $stmt->bindValue($key, $val, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue($key, $val, PDO::PARAM_STR);
                    }
                }
            }
            $stmt->execute();
            return true;
        } catch (PDOException $e) {
            echo "<pre>";
            print_r([
                'status' => 'error',
                'type' => 'tryCatch',
                'sql' => $sql,
                'msgErr' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function querySQL($sql, $bindParam = [])
    {
        if (!empty($bindParam)) {
            foreach ($bindParam as $key => $value) {
                $escapedValue = $this->escapeValue($value);
                $sql = str_replace(":$key", $escapedValue, $sql);
            }
        }
        return $sql;
    }

    public function execute($sql, $bindParam = [])
    {
        try {
            // เริ่ม Transaction
            $this->dbConn->beginTransaction();

            $stmt = $this->dbConn->prepare($sql);

            // วนลูป Bind Parameters
            if (is_array($bindParam) && count($bindParam) > 0) {
                foreach ($bindParam as $key => $val) {
                    // จัดการค่าว่างและ NULL (ตามสไตล์เมธอด insert/update เดิม)
                    $val = isset($val) && !is_null($val) ? trim($val) : "";
                    $val = !empty($val) ? $val : null;

                    if (gettype($val) == "integer") {
                        $stmt->bindValue($key, $val, PDO::PARAM_INT);
                    } else {
                        $stmt->bindValue($key, $val, PDO::PARAM_STR);
                    }
                }
            }

            // ประมวลผล
            if ($stmt->execute()) {
                // สำเร็จ -> Commit
                $this->dbConn->commit();

                // คืนค่าผลลัพธ์ (จำนวนแถวที่ได้รับผลกระทบ และ Last ID เผื่อกรณี Insert)
                return [
                    'status' => 'success',
                    'rowCount' => $stmt->rowCount(),
                    'lastInsertID' => $this->dbConn->lastInsertId()
                ];
            } else {
                // ไม่สำเร็จ -> Rollback
                $this->dbConn->rollBack();
                echo "<pre>";
                print_r([
                    'status' => 'error',
                    'type' => 'execute',
                    'msgErr' => 'Execution failed'
                ]);
                exit;
            }
        } catch (PDOException $e) {
            // เกิด Error -> Rollback
            $this->dbConn->rollBack();
            echo "<pre>";
            print_r([
                'status' => 'error',
                'type' => 'tryCatch',
                'sql' => $sql,
                'msgErr' => $e->getMessage()
            ]);
            exit;
        }
    }
}
