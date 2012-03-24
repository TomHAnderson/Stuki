<?php

/**
 * This model is used to run StuQL queries
 */
namespace StuQL\Model;

use StuQL\Exception,
    Stuki\Model\Model,
    Doctrine\ORM\AbstractQuery as Query
    ;

class StuQL extends Model
{
    /**
     * This is the public function for this model
     */
    public function query($statement) {
        set_time_limit(600); # FIXME:  is this needed here?

        $return = $keys = array();

        $parser = new \PHPSQLParser\Parser;
        $query = $parser->parse($statement);
        $parts = array_keys($query);

        // Don't continue if the query didn't parse
        if (!isset($parts[0]))
            throw new Exception("Unable to parse query");

#print_r($query);die();
        /**
         * This switch handles the different query types
         * supported by StuQL
         */
        switch ($parts[0]) {
            case 'SELECT':
                // Get all keys for the attribute set
                $keys = $this->fetchAllKeys($query['FROM']);
                $keys = $this->filterWhereClause($keys, $query, $parts[0]);
                $keys = $this->filterLimit($keys, $query);

                // If we have no records return
                if (!$keys) return array();

                // Fetch the values for the filtered keys
                $result = $this->fetchSelectColumns($keys, $query);
                break;

            case 'UPDATE':
                // Get all keys for the attribute set
                $keys = $this->fetchAllKeys($query['UPDATE']);
                $keys = $this->filterWhereClause($keys, $query, $parts[0]);
                $keys = $this->filterLimit($keys, $query);

                // If we have no records return
                if (!$keys) return 0;

                // Update all values in the filtered keys
                $keys = $this->updateValues($keys, $query);

                // Return the # of rows updated
                $result = sizeof($keys);
                break;
            case 'DELETE':
                // Verify only one table is to be deleted
                if (sizeof($query['DELETE']['TABLES']) != 1)
                    throw new Exception("You may only delete from one table at a time");

                // Get all keys for the attribute set
                $keys = $this->fetchAllKeys($query['FROM']);
                $keys = $this->filterWhereClause($keys, $query, $parts[0]);
                $keys = $this->filterLimit($keys, $query);

                // Delete entities
                $entities = $this->getLocator()->get('modelEntities');
                foreach ((array)$keys as $key)
                    $entities->delete($entities->find($key));

                $result = sizeof($keys);
                break;
            default:
                throw new Exception('This query type is not supported');
                break;
        }

        return $result;
    }


    /**
     * Used for LIKE expressions
     */
    private function convertLikeToRegex($string) {
        return "/^" . str_replace( '%', '(.*?)', preg_quote( $string ) ) .  "$/s";
    }


    /**
     * This parses the from section of a query.  This varies based on the query
     * type so the query part is passed in.
     */
    private function fetchAllKeys($queryPart) {
        // Only allow one table
        if (sizeof($queryPart) > 1)
            throw new Exception("Only one table per query is supported");

        // Verify all FROM tables exist
        foreach ($queryPart as $table) {
            #FIXME:  This doesn't work with multiple tables or joins
            // Start by fetching all possible keys
            $keysQuery = $this->getEm()->createQuery('
                SELECT e.entity_key
                  FROM Entities\Entities e, Entities\AttributeSets AS asets
                 WHERE asets.code = ?1
                   AND e.attributeSet = asets.attribute_set_key
            ');
            $keysQuery->setParameter(1, $table['table']);
            $resKeys = $keysQuery->getResult(Query::HYDRATE_SCALAR);
        }
        foreach ($resKeys as $row) {
            $keys[] = $row['entity_key'];
        }

        return $keys;
    }


    /**
     * This parses the where section of a query
     */
    private function filterWhereClause($keys, $query, $queryType) {
        if (!isset($query['WHERE'])) return $keys;
        // Filter values based on where
        $table = $column = $operator = $value = '';
        foreach ($query['WHERE'] as $where) {
            switch ($where['expr_type']) {
                case 'colref':
                    // Check for special columns
                    switch ($where['base_expr']) {
                        case 'entity_key':
                            $table = '';
                            $column = 'entity_key';
                            $specialColumn = true;
                            break;

                        default:
                            // A column to be acted upon
                            $table = strtok($where['base_expr'], '.');
                            $column = strtok('.');
                            $specialColumn = false;
                            break;
                    }

                    switch ($queryType) {
                        case 'UPDATE':
                            $tables = $query['UPDATE'];
                            break;
                        case 'SELECT':
                        case 'DELETE':
                            $tables = $query['FROM'];
                            break;
                        default:
                            throw new Exception('Invalid query type');
                    }

                    // Match table alias
                    foreach ($tables as $fromTable) {
                        if ($fromTable['alias'] == $table) {
                            $table = $fromTable['table'];
                            break;
                        }
                    }

                    // Verify table exists
                    $found = false;
                    foreach ($tables as $fromTable) {
                        if ($table == $fromTable['table']) {
                            $found = true;
                            break;
                        }
                    }
                    if (!$specialColumn AND !$found)
                        throw new Exception("Table or alias $table not found in FROM clause for " . $where['base_expr']);

                    break;
                case 'operator':
                    $operator = $where['base_expr'];
                    break;
                case 'const':
                    $value = $where['base_expr'];
                    // Remove quotes from search value
                    if ($value == "''") $value = '';
                    $value = str_replace("''","~stuql_quote~", $value);
                    $value = str_replace("'","", $value);
                    $value = str_replace("~stuql_quote~", "'", $value);

                    if ($specialColumn) {
                        $keys = $this->filterSpecial($keys, $column, $operator, $value);
                    } else {
                        $keys = $this->filter($keys, $table, $column, $operator, $value);
                    }

                    $table = $column = $operator = $value = '';
                    $specialColumn = false;
                    break;
                case 'in-list':
                    // Don't filter value for in lists
                    $value = $where['base_expr'];

                    if ($specialColumn) {
                        $keys = $this->filterSpecial($keys, $column, $operator, $value);
                    } else {
                        $keys = $this->filter($keys, $table, $column, $operator, $value);
                    }

                    $table = $column = $operator = $value = '';
                    $specialColumn = false;
                    break;
                case 'reserved':
                default:
                    throw new \StuQL\Exception('Where segment ' . $where['expr_type'] . ' is not supported');
                    break;
            }
        }

        // Are there any orphaned criteria?
        if ($table OR $column OR $operator OR $value)
            throw new Exception("An orphanced where criteria exists.  Your query is invalid.");

        return $keys;
    }


    /**
     * If a limit exists for the query cut
     * the resulting keys to the limit
     */
    private function filterLimit($keys, $query) {
        // Filter to LIMIT
        if (isset($query['LIMIT'])) {
            $keys = array_slice($keys, $query['LIMIT']['start'], $query['LIMIT']['end']);
        }

        return $keys;
    }


    /**
     * Update an EAV value
     * or delete if null or empty string
     * in the database
     */
    private function update($entity_key, $table, $column, $value) {
        $entities = $this->getLocator()->get('modelEntities');
        $attributes = $this->getLocator()->get('modelAttributes');

        $nullEntity = new \Entities\Entities;
        foreach ($nullEntity->valueEntities as $valueEntityName) {
            $valueQuery = $this->getEm()->createQuery("
                SELECT val
                  FROM Entities\Entities entity,
                       Entities\AttributeSets asets,
                       Entities\Attributes att,
                       $valueEntityName as val
                 WHERE entity.attributeSet = asets
                   AND att.attributeSet = asets
                   AND val.attribute = att
                   AND val.entity = entity
                   AND entity = ?1
                   AND asets.code = ?2
                   AND att.code = ?3
            ");
            $valueQuery->setParameter(1, $entity_key);
            $valueQuery->setParameter(2, $table);
            $valueQuery->setParameter(3, $column);

            if ($valueEntity = $valueQuery->getResult()) break;
        }

        // If a value does not exist just return an empty string
        if ($valueEntity AND $valueEntity[0]) {
            if (sizeof($valueEntity) != 1)
                throw new \StuQL\Exception("Too many value entities were returned from StuQL query.  This is an unexpected error.");
            $valueEntity = $valueEntity[0];
        } else {
            // Value not found, add it
            $entity = $entities->find($entity_key);
            $attribute = $attributes->findOneBy(array(
                'attributeSet' => $entity->getAttributeSet(),
                'code' => $column
            ));
            $renderer = $attribute->getRenderer();
            $datatype = $renderer->getClassObject('stuql')->getDataType();
            $class="\Entities\Values$datatype";
            $valueEntity = new $class;
            $valueEntity->setEntity($entity);
            $valueEntity->setAttribute($attribute);
            $this->getEm()->persist($valueEntity);
        }

        // Only save the value if there is a value
        if ($value === null OR $value === '') {
            $this->getEm()->remove($valueEntity);
        } else {
            $valueEntity->setValue($value);
        }
        return true;
    }


    /**
     * Return a single eav column value
     */
    private function getValue($entity_key, $table, $column) {
        $returnKeys = array();
        $valueEntity = null;

        // Find value entity
        $nullEntity = new \Entities\Entities;
        foreach ($nullEntity->valueEntities as $valueEntityName) {
            $valueQuery = $this->getEm()->createQuery("
                SELECT val
                  FROM Entities\Entities entity,
                       Entities\AttributeSets asets,
                       Entities\Attributes att,
                       $valueEntityName as val
                 WHERE entity.attributeSet = asets
                   AND att.attributeSet = asets
                   AND val.attribute = att
                   AND val.entity = entity
                   AND entity = ?1
                   AND asets.code = ?2
                   AND att.code = ?3
            ");
            $valueQuery->setParameter(1, $entity_key);
            $valueQuery->setParameter(2, $table);
            $valueQuery->setParameter(3, $column);

            if ($valueEntity = $valueQuery->getResult()) break;
        }

        // If a value does not exist just return an empty string
        if (!$valueEntity) return '';
        if (sizeof($valueEntity) != 1)
            throw new \StuQL\Exception("Too many value entities were returned from StuQL query.  This is an unexpected error.");

        $valueEntity = $valueEntity[0]; # get only row of result

        # FIXME:  This trim is a hack; fix entities?
        return trim($valueEntity->getValue());
    }


    /**
     * Filter special fields
     */
    private function filterSpecial($keys, $column, $operator, $value) {
        $returnKeys = array();

        switch ($column) {
            case 'entity_key':
                // Loop through all keys and filter
                foreach ($keys as $key) {
                    switch (strtolower($operator)) {
                        case '=':
                            if ($value == $key)
                                $returnKeys[] = $key;
                            break;
                        case '>':
                            if ($value > $key)
                                $returnKeys[] = $key;
                            break;
                        case '<':
                            if ($value < $key)
                                $returnKeys[] = $key;
                            break;
                        case 'in':
                            // Tokenize value
                            $value = str_replace('(', '', $value);
                            $value = str_replace(')', '', $value);
                            $values = explode(',', $value);

                            if (in_array($key, $values))
                                $returnKeys[] = $key;
                            break;
                        default:
                            throw new \StuQL\Exception('The operator ' . $operator . ' is not supported for entity_key special field type.');
                            break;
                    }
                }

                return $returnKeys;
                break;
            default:
                throw new Exception("Invalid special column ($column) found");
                break;
        }

    }

    /**
     * Filter the given keys based on the filter criteria
     */
    private function filter($keys, $table, $column, $operator, $value) {
        $returnKeys = array();
        $valueEntity = null;

        foreach ($keys as $entity_key) {

            // Find value
            $checkValue = $this->getValue($entity_key, $table, $column);

            // Compare check value with operator
            switch (strtolower($operator)) {
                case '=':
                    if ($value == $checkValue)
                        $returnKeys[] = $entity_key;
                    break;
                case '>':
                    if ($value > $checkValue)
                        $returnKeys[] = $entity_key;
                    break;
                case '<':
                    if ($value < $checkValue)
                        $returnKeys[] = $entity_key;
                    break;
                case 'like':
                    $likeValue = $this->convertLikeToRegex($value);
                    if (preg_grep($likeValue, array($checkValue)))
                        $returnKeys[] = $entity_key;
                    break;
                case 'notlike':
                    $likeValue = $this->convertLikeToRegex($value);
                    if (!preg_grep($likeValue, array($checkValue)))
                        $returnKeys[] = $entity_key;
                    break;
                case 'in':
                    // Remove quotes from search value
                    if (!is_array($value)) {
                        $value = explode(',', $value);
                    }
                    $values = array();

                    # FIXME:  this is jankey
                    foreach ($value as $v) {
                        if ($v == "''") $v = '';
                        $v = str_replace('(', '', $v);
                        $v = str_replace(')', '', $v);

                        $v = str_replace("''","~stuql_quote~", $v);
                        $v = str_replace("'","", $v);
                        $v = str_replace("~stuql_quote~", "'", $v);

                        $values[] = $v;
                    }

                    if (in_array($checkValue, $values))
                        $returnKeys[] = $entity_key;
                    break;

                default:
                    throw new \StuQL\Exception('The operator ' . $operator . ' is not supported');
                    break;
            }
        }

#print_r($returnKeys);die();
        return $returnKeys;
    }


    public function fetchSelectColumns($keys, $query) {
        $attributeSets = $this->getLocator()->get('modelAttributeSets');

        // Verify all FROM tables exist
        $result = array();
        foreach ($query['FROM'] as $table) {
            $attributeSet = $attributeSets->findOneBy(array(
                'code' => $table['table']
            ));
            if (!$attributeSet)
                throw new Exception("The attribute set '$table[table]' does not exist");

            // Verify all attributes exist
            foreach ($query['SELECT'] as $column) {

                // We don't want `` around our aliases
                $column['alias'] = str_replace('`', '', $column['alias']);

                // All columns must reference their table as table.column
                $columnPrefix = strtok($column['base_expr'], '.');
                $columnName = strtok('.');

                // Only check columns for this table
                if ($table['alias'] != $columnPrefix)
                    throw new Exception("An invalid table alias was found");

                $columnTable = $table['table'];

                // Does the attribute exist?
                switch ($columnName) {
                    case '*':
                        // Add all columns
                        foreach ($attributeSet->getAttributes() as $attribute) {
                            $columnName = $attribute->getCode();
                            foreach ($keys as $entity_key) {
                                $result[$entity_key][$columnPrefix . '.' . $columnName] = $this->getValue($entity_key, $columnTable, $columnName);
                            }
                        }
                        break;

                    default:
                        // Add a single column
                        foreach ($keys as $entity_key) {
#                            print_r($column);die();
                            $result[$entity_key][$column['alias']] = $this->getValue($entity_key, $columnTable, $columnName);
                        }
                        break;
                }
            }
        }

        return $result;
    }

    public function updateValues($keys, $query) {
        $attributeSets = $this->getLocator()->get('modelAttributeSets');

        // Update all values
        foreach ($query['UPDATE'] as $table) {
            $attributeSet = $attributeSets->findOneBy(array(
                'code' => $table['table']
            ));
            if (!$attributeSet)
                throw new Exception("The attribute set '$table[table]' does not exist");

            // Verify all attributes exist
            foreach ($query['SET'] as $column) {
                // All columns must reference their table as table.column
                $columnTable = strtok($column['column'], '.');
                $columnName = strtok('.');

                // Only check columns for this table
                if ($table['alias'] != $columnTable) continue;
                $columnTable = $table['table'];

                // Does the attribute exist?
                switch ($columnName) {
                    default:
                        // Update a single column
                        foreach ($keys as $entity_key) {
                            // Remove quotes from update value
                            $value = $column['expr'];
                            if ($value == "''") $value = '';
                            $value = str_replace("''","~stuql_quote~", $value);
                            $value = str_replace("'","", $value);
                            $value = str_replace("~stuql_quote~", "'", $value);

                            $this->update($entity_key, $columnTable, $columnName, $value);
                        }
                        break;
                }
            }
        }

        $this->getEm()->flush();

        return $keys;
    }
}