SET @database_name = 'conf2';
SET @table_name = 'conferences';

-- Show all constraints
SELECT 
    CONSTRAINT_NAME,
    CONSTRAINT_TYPE,
    TABLE_NAME
FROM information_schema.TABLE_CONSTRAINTS
WHERE TABLE_SCHEMA = @database_name 
AND TABLE_NAME = @table_name;

-- Show check constraints specifically
SELECT 
    CONSTRAINT_NAME,
    CHECK_CLAUSE
FROM information_schema.CHECK_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = @database_name;

-- Show table structure
DESCRIBE conferences;

-- Show create table statement
SHOW CREATE TABLE conferences;
