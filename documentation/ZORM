==ZORM: The Zedek Object Relational Mapper==

Zedek Framework implements a basic ORM (Object Relational Mapper). This can be called from any point by instantiating the ZORM class and this returns a PDO object.

    $orm = new ZORM();

This object can map to existing tables via the ZORMTable class. For existing tables the they may be accessed by calling the table method on the ZORM object.

    $table = $orm->table("table_name");

Where the table does not exist it may be created using the same method but passing an array that defines the table with the column name as key in the array and attributes as the array values:

    $array = array(
    	'id'=>"int primary key auto_increment", 
    	'name'=>"varchar(30)", 
    	'address'=>"text", 
    	'created_on'=>"timestamp", 
    );

    $table = $orm->table("table_name", $array);

This will create the table and return the ZORMTable object.

===ZORMTable Methods===

The fetch method returns all of the tables contents as an array:

    $table->fetch()

This is much like running the query:

    SELECT * FROM table_name

The next method is the add (same as the Create in CRUD), this takes an array as argument and creates a new entry in the table

    $array(
    	'name'=>"James Bond", 
    	'address'=>"Somewhere in the UK", 
    );
    $table->add($array); 

This will be equivalent to:

    INSERT INTO table_name (name, address) VALUES ('James Bond', 'Somewhere in the UK');

The remove method (Delete in CRUD) will remove one or more entries from the table it takes 1 or 2 arguments. When taking a single argument it assumes that the entry is an integer value maped to an id column:

    $table->remove(44);

This maps to:

    DELETE FROM table_name WHERE id=44;

When taking 2 arguments it would be of the form:

    $table->remove('James Bond', 'name');

This maps to:

    DELETE FROM table_name WHERE name='James Bond';

Next stop update. This method takes 3 arguments being the array of values being updated, the value and column to be updated:

    array(
    	'address'=>"Another place in the UK", 
    );
    $table->update($array, 'James bond', 'name');

This would map to:

    UPDATE table_name SET address='Another place in the UK' WHERE name='James Bond';

The drop mthod drops the table:

    $table->drop();

the row method returns a table row object (ZORMRow). It ordinarily takes one or 2 arguments. Where a single integer argument is passed it assumes this is an id.

    $table->row(12);

This will map to a row:

	SELECT * FROM table_name WHERE id=12;

or:

    $row = $table->row('James Bond', 'name');

this maps to:

    SELECT * FROM table_name WHERE name='James Bond' LIMIT 1;


===ZORMRow===

The ZORMRow object maps to a single database table row and maps attriutes to each column entry:

    echo $row->name;

will return "James Bond". and 

    echo $row->address;

will return "Another place in the UK".

new values may be assigned to these attributes but these do not get writen to the database till the commit method is run.

    $row->name = "Austin Powers";
    $row->commit();

This will be equivalent to:

    UPDATE table_name SET name='Austin Powers' WHERE id={$row->id};

