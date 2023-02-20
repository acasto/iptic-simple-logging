# Description #
This plugin provides a simple mechanism to log events to a database table and display them in the admin area.

The plugin will log events to the **ipticsl_log** table, which will be created automatically if it does
not already exist. The table will have the following columns:

- **id** - the primary key
- **time** - the timestamp of the event
- **message** - the human-readable message logged (e.g. 'The user logged in')
- **data** - a place to store more machine-readable data (e.g. a user or event id)
- **user** - the user the event was logged for (e.g. 'admin')
- **facility** - the facility the event was logged to (e.g. 'myplugin')
- **level** - the level of the event (e.g. 'info', 'warning', 'error')


## Usage ##

The following events are logged by default:

- user login
- user logout
- user registration
- user password reset
- user profile update
- user deleted

This default logging behavior can be disabled by adding the following to your theme's functions.php file or similar:
```
    add_filter( 'isl_default_logging', '__return_false' );
```

For logging your own events, while you can log with *\Iptic\SL\Log::log()* directly, it is recommended that you create your 
own wrapper function so that you can more easily customize the default values. The same with *\Iptic\SL\Log::get_logs()*
since it will require handling of the returned object or array anyways (e.g. for use in a shortcode). 

### Methods ###

The **\Iptic\SL\Log::log()** method takes the following parameters:

```log( $message, array $args  )```
- **$message** (string): the message to log (e.g. 'The user logged in')
- **$args** (array): an array of arguments to pass to the log method. The following arguments are supported:
  - **user** : the user the event was logged for (e.g. 'admin') (defaults to the current user)
  - **facility** : the facility the event was logged to (e.g. 'myplugin') (defaults to null)
  - **level** : the level of the event (e.g. 'info', 'warning', 'error') (defaults to null)
  - **data** : additional data to log (e.g. some machine-readable data) (defaults to null)
  - **tz_local** : set to **true** to use the local timezone for the timestamp (default is UTC)

The **\Iptic\SL\Log::get_logs()** method takes the following parameters:

```get_logs( array $args, string $output = 'OBJECT' )```
- **$args** (array): an array of arguments to pass to the log method. The following arguments are supported:
  - **user** : the user the event was logged for (for where comparison)
  - **facility** : the facility the event was logged to (for where comparison)
  - **level** : the level of the event (for where comparison)
  - **message** : the message to log (for where comparison - might want to consider setting 'comp' to 'LIKE')
  - **data** : additional data to log (for where comparison)
  - **comp** : the comparison operator to use for the column filters (e.g. LIKE, '>') (defaults to '=')
  - **sort** : the sort order (defaults to 'DESC')
  - **order_by** : the column to sort by (default is entry order since timestamp precision is only to the second)
  - **limit** : the limit for the query (defaults to all)
  - **offset** : the offset for the query (defaults to null)
- **$output** (string): Any of ARRAY_A | ARRAY_N | OBJECT | OBJECT_K constants (defaults to OBJECT)
  - See: https://developer.wordpress.org/reference/classes/wpdb/get_results/


### Filters ###
The following filters are available:

- **isl_default_logging** - can be used to disable the default logging facilities of the plugin by returning false.
    - Example: ```add_filter( 'isl_default_logging', '__return_false' );```

- **isl_admin** - can be used to disable the admin display for the plugin by returning false.
  - Example: ```add_filter( 'isl_admin', '__return_false' );```

- **ipticsl_menu_name** - can be used to change the menu name for the plugin.

- **ipticsl_log_table_atts** - can be used to change the attributes for the log table.
Current attributes and defaults are:
    ```
    'user' => '',   // user column - can be used to filter for a particular user
    'facility' => '', // facility column - can be used to filter for a particular facility
    'level' => '', // level column - can be used to filter for a particular level
    'message' => '', // message column - can be used to filter on a particular message
    'data' => '', // data column - can be used to filter on a particular data value
    'tz_local' => 'true', // show the time in the local timezone
    'comp' => '', // comparison operator for when using the column filters
    'sort' => '', // default to DESC
    'order_by' => '', // column to sort by (default is entry order since timestamp precision is only to the second)
    'limit' => '500', // an arbitrary limit
    'offset' => '', // offset for the limit if desired
    ```


## Changelog ##

### 0.1.0 ###
- Initial release - 02/20/2023

