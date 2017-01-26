<?php

/**
 * @author: Reinier Gombert
 * @date: 16-nov-2016
 * 
 * Data Access Layer
 */
class DAL
{

    private $link;
    private $server, $userName, $password, $db;
    private $lastQueryResult, $lastMysqlError, $lastQuery;

    public function __construct($server = DB_HOST, $userName = DB_USER, $password = DB_PASS, $db = DB_NAME)
    {
        $this->server = $server;
        $this->userName = $userName;
        $this->password = $password;
        $this->db = $db;
        $this->lastQueryResult = null;
        $this->lastMysqlError = null;
        $this->lastQuery = null;

        // start connection with db
        $this->connection();
    }

    protected function connection()
    {
        try
        {
            $this->link = new PDO("mysql:host=" . $this->server . ";dbname=" . $this->db . ";charset=utf8", $this->userName, $this->password);
            // set the PDO error mode to exception
            $this->link->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        catch (Error $t)
        {
            error_log("Error thrown: " . $t->getMessage() . "\n");
            return null;
        }
        catch (PDOException $e)
        {
            error_log("PDO-Exception: " . $e->getMessage() . "\n");
            return null;
        }
    }

    protected function close(&$pdo)
    {
        $pdo = null;
    }

    public function getLastQueryResult()
    {
        return $this->lastQueryResult;
    }

    public function getLastMysqlError()
    {
        return $this->lastMysqlError;
    }

    public function getLastQuery()
    {
        return $this->lastQuery;
    }

    public function openConnection()
    {
        return $this->connection();
    }

    public function closeConnection()
    {
        // do nothing
    }

    //xss mitigation functions
    public function xssafe($data, $encoding = 'UTF-8')
    {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
    }

    public function xecho($data)
    {
        echo xssafe($data);
    }

    /**
     * This method executes the given SQL-query.
     * It will open the connection with the database and closes it immediately after the query has been processed.
     * 
     * @param String $sql                       The query to process.
     * @param String $flag                      If provided with a flag, the return will be in the type of the flag mentioned. Options are: 'object' and 'array', default = 'object'. 
     * @param String $count                     to determine whether to return multiple rows or just one, or the value of one column. Options are 'one' and 'multiple' and 'column'.
     * @return boolean|null|array|object        Returns a boolean if a SELECT- or SHOW-query succeeded, returns the inserted id of an INSERT-query, returns 'null' if no results were found. Based on the flag given, returns the result of the query.
     */
    protected function query($sql, $aParams = null, $count = "multiple", $flag = "object")
    {
        // initialize 
        $pdo = $this->link;
        try
        {
            // prepare sql
            $stmt = $pdo->prepare($sql);
            if (!$stmt)
            {
                throw new Error;
            }
            // bind params
            if (!is_null($aParams))
            {
                if (is_array($aParams))
                {
                    foreach ($aParams as $paramName => $param) // bind parameters to the query
                    {
                        // if the param is an array, the the datatype has also been given
                        if (is_array($param))
                        {
                            $stmt->bindParam($this->xssafe($paramName), $param[0], $param[1]);
                        }
                        else
                        {
                            $stmt->bindParam($this->xssafe($paramName), $param, PDO::PARAM_STR);
                        }
                    }
                }
                else
                {
                    throw new Error;
                }
            }
            // execute for result
            if (!$stmt->execute())
            {
                throw new Error;
            }
            // set res on null
            $res = null;

            if (strpos($sql, 'SELECT') !== false || strpos($sql, 'SHOW') !== false) // if SELECT or SHOW, then fetch the rows
            {
                // get result
                if ($stmt->rowCount())
                {
                    $res = ($count === "one" ? $stmt->fetch($flag === "array" ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ) : ($count === "column" ? $stmt->fetchColumn() : $stmt->fetchAll($flag === "array" ? PDO::FETCH_ASSOC : PDO::FETCH_OBJ)));
                }
            }

            // save errors and results
            $this->lastQueryResult = $res;
            $this->lastQuery = $sql;

            if (strpos($sql, 'INSERT') !== false) // return inserted id after INSERT-statement
            {
                $lastid = $pdo->lastInsertId();
                // close the PDO
                $this->close($pdo);
                return $lastid;
            }
            else if (strpos($sql, 'SELECT') === false && strpos($sql, 'SHOW') === false) // if neither SELECT nor SHOW statements were given, then immediately return true, for no results have to be given back
            {
                // close the PDO
                $this->close($pdo);
                return true;
            }


            // close the PDO
            $this->close($pdo);

            // result failed
            if (!$res)
            {
                if (strpos($sql, 'SELECT') === false && strpos($sql, 'SHOW') === false) // if not SELECT or SHOW, then return false
                {
                    return false; // the insert or update didn't work
                }
                else // else return null
                {
                    return null; // the select or show has null results
                }
                throw new Error;
            }

            // return result if no errors, no updates or inserts
            // so basically only the result of a SELECT- or SHOW- query
            return $res;
        }
        catch (Error $t)
        {
            $this->lastMysqlError = $t->getMessage();
            error_log("Error: " . $t->getMessage() . ". \n\nSQL: " . $sql . "\n");
        }
        catch (PDOException $e)
        {
            $this->lastMysqlError = $e->getMessage();
            error_log("PDO-Exception: " . $e->getMessage() . ". \n\nSQL: " . $sql . "\n");
        }
        catch (Exception $ex)
        {
            $this->lastMysqlError = $ex->getMessage();
            error_log("PDO-Exception: " . $ex->getMessage() . ". \n\nSQL: " . $sql . "\n");
        }
        // close the PDO
        $this->close($pdo);
        return false;
    }

    /**
     * Returns a string of a field prepared for AES_DECRYPTION.
     * 
     * @param string $field Name of a field in the database.
     * @return string
     */
    protected function getDecryptFieldString($field)
    {
        return "AES_DECRYPT(" . $field . ", '" . SALT . "') AS " . $field;
    }

    /**
     * Returns a string of a value prepared for AES_ENCRYPTION.
     * 
     * @param string $value Value to be encrypted
     * @return string
     */
    protected function getEncryptValueString($value)
    {
        return "AES_ENCRYPT(" . $value . ", '" . SALT . "')";
    }

    /**
     * Returns a string of multiple fields prepared for AES_DECRYPTION
     * 
     * @param array $fields Array of fields in the database
     * @param boolean $checkType    Indicates whether to check on the column's datetype
     * @return string
     */
    protected function getFieldsAsDecryptedString($fields, $checkType = false)
    {
        $string = "";
        foreach ($fields as $field => $value)
        {
            if ($string !== "")
            {
                $string .= ", ";
            }

            if ($checkType)
            {
                if ($value == "varbinary(150)")
                {
                    $string .= $this->getDecryptFieldString($field);
                }
                else
                {
                    $string .= $field;
                }
            }
            else
            {
                $string .= $this->getDecryptFieldString($value);
            }
        }
        return $string;
    }

    /**
     * Returns an array of fields in the format: fieldName => Type( = MySQL dataType).
     * 
     * @param string $tableName Name of table in database
     * @return array
     */
    protected function getFieldsOfTable($tableName)
    {
        $sql = "SHOW COLUMNS 
                FROM " . $tableName;
        $result = $this->query($sql);

        $return = array();
        foreach ($result as $tableColumn)
        {
            $return[$tableColumn->Field] = $tableColumn->Type;
        }
        return $return;
    }

    /**
     * Returns a string of all fields of a table prepared for AES_DECRYPTION
     * 
     * @param string $tableName Name of the table.
     * @return string
     */
    protected function getDecryptedTableFields($tableName)
    {
        $fields = $this->getFieldsOfTable($tableName);
        return $this->getFieldsAsDecryptedString($fields, true);
    }

    /**
     * This method is for dynamically inserting a record into the DB,
     * based on the table and the parameters given.
     * 
     * @param string $table     Table name
     * @param array $aParams    Example: array(ColumnNameOfTable_1 => ValueToBeInserted_1, ColumnNameOfTable_2 => ValueToBeInserted_2)
     * @return int              The inserted ID
     */
    protected function insert($table, $aParams)
    {
        // prepare vars
        $fields = ""; // fields of table that will be inserted
        $values = ""; // values of these fields
        $aQueryParams = array(); // parameters for PDO's bindParam
        
        // get field specifations of the table to see when to use encryption
        $tableFields = $this->getFieldsOfTable($table);

        // loop through the given array
        foreach ($aParams as $columnName => $value)
        {
            // insert into fields
            $fields .= ($fields === "" ? $columnName : ", " . $columnName);

            // check if this value needs to be encrypted based on datatype of field
            if ($tableFields[$columnName] == "varbinary(150)")
            {
                $values .= ($values === "" ? $this->getEncryptValueString(":" . $columnName) : ", " . $this->getEncryptValueString(":" . $columnName));
            }
            else // do not encrypt value
            {
                $values .= ($values === "" ? ":" . $columnName : ", :" . $columnName);
            }
            
            // add parameter to the array for PDO's bindParam
            $aQueryParams[":" . $columnName] = array($value, (is_numeric($value) ? PDO::PARAM_INT : PDO::PARAM_STR));
        }

        // create the insert query
        $sql = "INSERT INTO " . $table . " (" . $fields . ")
                VALUES (" . $values . ")";
        $insertedID = $this->query($sql, $aQueryParams);

        // return the insertedID
        return $insertedID;
    }

}
