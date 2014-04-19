<?php
/**
 * Pop Up Archive WordPress Plugin
 *
 * @category  File
 * @package   Popuparchive-WP
 * @author    Thomas Crenshaw <thomas@circadigital.biz>
 * @copyright 2014 Thomas Crenshaw <thomas@circadigital.biz>
 * @license   
 * @link      https://circadigital.biz/
 */
require_once 'includes/Services/Popuparchive.php';

if (!class_exists('Popuparchive_WP_List_Table')) {
    include_once(dirname(__FILE__).'/includes/global/class-puawp-list-table.php');
}

/**
 * Pop Up Archive WP Plugin audio items display class
 *
 * This class extends a derived version of the infamous WP_List_Table class
 *
 * @category  Services
 * @package   Popuparchive-WP\Popuparchive_Audio_Items_Display
 * @author    Thomas Crenshaw <thomascrenshaw@gmail.com>
 * @copyright 2014 Pop Up Archive <info@popuparchive.org>
 * @license   GNU AFFERO GENERAL PUBLIC LICENSE <http://www.gnu.org/licenses/agpl.html>
 * @link      https://www.popuparchive.com/
 */
class Popuparchive_Audio_Items_Display extends Popuparchive_WP_List_Table
{
    /**
     * Class constructor
     *
     * @return void
     *
     * @access public
     */
    public function __construct()
    {
        //Set parent defaults
        parent::__construct( array(
            'singular'  => 'audio item',     //singular name of the listed records
            'plural'    => 'audio items',    //plural name of the listed records
            'ajax'      => false        //does this table support ajax?
        ) );

    } // end __construct()

   /**
     * returns the item that is displayed in a column of matching name
     *
     * @param array item        item
     * @param string column_name column name
     *
     * @return mixed
     *
     * @access public
     */
    public function column_default($item, $column_name)
    {
        return $item[$column_name];
    }

    /**
     * Function column_title
     *
     * @param string item
     *
     * @return html span
     *
     * @access public
     */
    public function column_title($item)
    {
        //Return the title contents
        return sprintf('%1$s <span style="color:silver"></span>%2$s',
            /*$1%s*/ $item['title'],
            /*$2%s*/ $this->row_actions($actions)
        );
    } // end column_title($item)

    /**
     * Sets up the columns for display
     *
     * @return array
     *
     * @access public
     */
    public function get_columns()
    {
        $columns = array(
            'display_title' => 'Title',
            'description' => 'Description',
            'shortcode' => 'PUA Item Shortcode',
            'tags' => 'WP Tags'
        );

        return $columns;
    } // end function get_columns()

    /**
     * Creates the sortable columns for display
     *
     * @return array
     *
     * @access public
     */
   public function get_sortable_columns()
    {
        $sortable_columns = array();

        return $sortable_columns;
    } // end function get_sortable_columns()

    public function get_modal_colums()
    {
        $columns = array(
            'display_title' => 'Title',
            'description' => 'Description',
            'shortcode' => 'Add Shortcode'
        );
        return $columns; 
    }


    /**
     * Prepares the list of items for displaying.
     *
     *
     * @return void
     *
     * @access public
     */
    public function prepare_items()
    {
        /* 20 seems like a good number to show */
        $per_page = 10;
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        $this->_column_headers = array($columns, $hidden, $sortable);

        /* Parameters that are used to order the results table */
        $orderby = !empty($_GET["orderby"]) ? mysql_real_escape_string($_GET["orderby"]) : 'id';
        $order = !empty($_GET["order"]) ? mysql_real_escape_string($_GET["order"]) : 'ASC';

        $current_page = $this->get_pagenum();
        $total_items = count($this->items);
        if ($total_items > 0) {

            $this->items = array_slice($this->items,(($current_page-1)*$per_page),$per_page);

            $this->set_pagination_args( array(
                'total_items' => $total_items,                  //WE have to calculate the total number of items
                'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
                'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
            ) );
        }
    } // end function prepare_items($data)

    /**
     * Function to set the protected class variable $items for use in the class
     *
     * @param array $items the list of audio items to display
     *
     * @access public
     */
    public function set_items($items)
    {
        $this->items = $items;
    }


    /**
     * Function to override output of list columns for the shortcode
     * 
     * Overriding the standard text output of the columns with a JQuery link to insert the shortcode into
     * the text box
     * 
     * @todo the plugin is only listing the first audio file. Need to update to show when there
     * are multiple audio files in the same audio item
     * 
     * @param object $item The current item
     * 
     * @return array 
     */
    public function column_shortcode($item)
    {
        /* the below string sets up the jQuery write back to the post by
        exposing the available shortcode related items. The tags would be done
        in the same fashion
        */
        //$shortcodeString = '<input id="puawpShortcode-'.$item['audio_file_ids'][0].
        //'" type="text" style="display:none;" value="'.$item['shortcode'].'"><a href="#" id="puawpShortcodeInsert-'.$item['audio_file_ids'][0].
        //'">Insert Pop Up Archive player</a>';

        /* this is a temp placeholder until the jQuery is put into place */
        $shortcodeString = $item['shortcode'];

        return $shortcodeString;
    }

    /**
     * Method to output the array of tags as a string
     *
     * @param object $item The current item
     * 
     * @access public
     * 
     * @return array 
     */
    public function column_tags($item)
    {
        return $this->array_to_string($item['tags']);

    }

    /**
     * Pop Up Archive Override Alert --
     *
     *
     *
     * @param object $item The current item
     *
     * @access public
     */
    public function array_to_string($array)
    {
        $string = "";
        for ($i=0; $i < count($array); $i++) {
            if ($i < count($array) - 1) {
                $string .= '"'.$array[$i].'",';
            } else {
                $string .= '"'.$array[$i].'"';
            }
        }

        return $string;
    }

} // end Class Popuparchive_Audio_Items_Display

/**
 * This method renders the admin items page
 *
 * @param boolean $rendering_modal true = rendering a modal
 * @param string  $post_id         id of the applicable post
 *
 * @todo separate out the view items from the controller items. Built this way to save time at the expense of technical debt
 *
 */
function renderAudioItemsList($rendering_modal = false, $post_id = '')
{
    /* get the audio item action */
    $audio_item_action = isset($_GET['action'])?$_GET['action']:'';

    /* get collections for the authenticated user */
    $collections_data = get_collections();

    if (isset($_POST['puawp_refresh_table'])) {
        $collection_id = $_POST['puawp-collections-filter'];
        /* get the audio data (after removing unused fields) */
        $flattened_audio = get_flat_audio_data($collections_data, $collection_id);
    } else {
        /* get the audio data (after removing unused fields) */
        $flattened_audio = get_flat_audio_data($collections_data);
        $collection_id = $collections_data['collections'][0]['id'];
    }
    $collection_name = get_collection_name($collection_id, $collections_data);

    if (!empty($collections_data)) {
        display_collections_page_top($collections_data, $collection_name, $rendering_modal, $post_id);
    } else {
        echo '<strong>No Collections Were Found That Matched Your Information</strong>';
    } // end if (!empty($collections))
    
    $display_table_msg = '<br /><div class="popuparchive_custom_blue_box">Displaying Results For <strong>'.strtoupper($collection_name).'</strong></div>';
    echo $display_table_msg;

    /* Create an instance of our audio items display class **/
    $audioItemsTableList = new Popuparchive_Audio_Items_Display();
    /* print out the update list of items */
    if (is_array($flattened_audio)) {
//        $audioItemsTableList->prepare_items($flattened_audio);
        $audioItemsTableList->set_items($flattened_audio);
        $audioItemsTableList->prepare_items();

        $audioItemsTableList->display();
    } else {
        echo $flattened_audio;
    }
?>
<?php
} // end function renderAudioItemsList()

/**
 * Helper function that sets the authorization code 
 * that was returned from the OAuth query
 *
 * @param puawp_options array contains the Pop Up Archive OAuth2 options
 *
 * @return object
 */
function puawp_set_access_token($puawp_options)
{
    $popuparchive = new Popuparchive_Services($puawp_options['puawp_client_id'], $puawp_options['puawp_client_secret'], $puawp_options['puawp_redir_uri_base'].$puawp_options['puawp_redir_uri_query']);
    $popuparchive->setAccessToken($puawp_options['puawp_access_token']);

    return $popuparchive;
} // end puawp_set_access_token

/**
 * Helper function that retreives the collections for the authorized 
 * user from the API
 *
 * @param collection_id string (optional) unique identifier of the currently selected collection
 *
 * @todo 1 change getPublicCollections to getting the users collection; dependency - OAuth issue resolved
 * @todo 2 update the return to something useful
 *
 * @return object
 */
function pua_get_collections($collection_ids = null)
{
    //get popuparchive options
    $puawp_options = get_option('popuparchive_settings');
    if ($puawp_options) {
        $puawp_client_id = $puawp_options['puawp_client_id'];
        $puawp_client_secret = $puawp_options['puawp_client_secret'];
        $puawp_access_token = $puawp_options['puawp_access_token'];
        $puawp_redir_uri = $puawp_options['puawp_redir_uri_base'].$puawp_options['puawp_redir_uri_query'];
    }
    $data = array();

    /* Check to see if the token is alredy set, if not return empty */
    /* @todo 2 */
    if (!$puawp_access_token) {
        $data = array("client authorization token is not set");

        return $data;
    }
    $popuparchive = puawp_set_access_token($puawp_options);

    try {
        /* @todo 1 */
        //$collections = $popuparchive->getPublicCollections(); //get all public collections
        $collections = $popuparchive->get('https://www.popuparchive.com/api/collections'); //get a user's collections
    } catch (Popuparchive_Services_Invalid_Http_Response_Code_Exception $e) {
        /* @todo: add check what kind of error and display message */
        $error_code = $e->getHttpCode();

        return $data; //for now if there is a problem return empty array
    }
    /* decode the collections JSON into an array and return */
    $data = json_decode($collections, true);

    return $data;
} // end pua_get_collections


/**
 * Helper function that retrieves the audio items from the API
 *
 * @param integer $collection_id id of the collection whose assets are being retreived
 *
 * @todo 1 update this to create a json error message that is passed back for display
 * @todo 2 create a json error message to pass back
 *
 * @return array
 */
function pua_get_audio_items($collection_id)
{
    /* get Pop Up Archive API options from the options array in the database */
    $puawp_options = get_option('popuparchive_settings');
    if ($puawp_options) {
        $puawp_client_id = $puawp_options['puawp_client_id'];
        $puawp_client_secret = $puawp_options['puawp_client_secret'];
        $puawp_access_token = $puawp_options['puawp_access_token'];
        $puawp_redir_uri = $puawp_options['puawp_redir_uri_base'].$puawp_options['puawp_redir_uri_query'];
    }
    $data = array();
    /* Check to see if the authorization token is already set, if not return empty array
     * @todo 1
     */
    if (!$puawp_access_token) {
        return $data;
    }

    /* set the authorization token in the main Popuparchive class */
    $popuparchive = puawp_set_access_token($puawp_options);
    try {
        $audio_items = $popuparchive->getItemsByCollectionId(strval($collection_id)); /* @todo get all items for a particular collection id */
    } catch (Popuparchive_Services_Invalid_Http_Response_Code_Exception $e) {
        /* @todo update this to check what kind of error and display message */
        $error_code = $e->getHttpCode();

        /* for the time being, returning an empty array if there is an issue
         * @todo 2
         */
        return $data;
        //exit($e->getMessage());
    }
    $bigdata = json_decode($audio_items, true); //decode json data into array

    $total_items = $bigdata['total_hits'];
    if($total_items == 0) {
        return "Your Collection does not contain any audio items";
    } else {
        $flat_data = flatten_the_data($collection_id, $bigdata);
        return $flat_data;
    }
} // end pua_get_audio_items

/**
 * Method to flatten the data, 
 * 
 * After the data is flattened, just the minimum needed for display and use in the
 * plugin. However, right now there are a few extra values in case the business
 * case changes for how the data is used.
 *
 * @param collection_id string unique identifier of the currently selected collection
 * @param audio_item    array  metadata pulled from the API that describes the audio item
 *
 * @todo update the function to create the array on the fly instead of creating variables
 * @todo update the function to write the array to the database for later use
 *
 * @return array $flat_item array of values that can be used for display
 */
function flatten_the_data($collection_id, $audio_items)
{
    $current_page = $audio_items['page'];
    if (isset($audio_items['results'])) {
        $results_items = $audio_items['results'];
        /* now lets break up the results into individual chunks */
        /* @todo validate that these fields are not null prior to setting them */
        foreach ($results_items as $item) {
            /* @todo update this to create the array on the fly */
            /* @todo write this directly to the database so that it is available */

            /* get the number of audio clips in the item */
            $audio_items_count = count($item['audio_files']); 
            /* if no audio clips, do not parse the item. just skip and begin the next item */
            if ($audio_items_count >= 1 && $item['id'] != '') {
                $item_id = strval($item['id']);
                $display_title = $item['title'];
                $description = !empty($item['description']) ? $item['description'] : "No Description Available";
                $embed_title = urlencode($item['title']);
                $collection_id = strval($item['collection_id']);
                $audio_file_ids = array();
                foreach ($item['audio_files'] as $audio_files) {
                    $audio_file_ids[] = strval($audio_files['id']);
                    /* embed code pattern is {TITLE}/{AUDIO_ID}/{ITEM_ID}/{COLLECTION_ID} */
                    $embed_code = $embed_title.'/'.$audio_files['id'].'/'.$item_id.'/'.$collection_id;
                }
                $tags = array();
                if (isset($item['tags'])) {
                    $tag_count = count($item['tags']);
                    for ($i=0; $i<$tag_count; $i++) {
                        $tags[] = $item['tags'][$i];
                    }
                } else {
                    if (isset($item['entities'])) {
                        $entity_count = count($item['entities']);
                        for ($i=0; $i<$entity_count; $i++) {
                            $tags[] = $item['entities'][$i]['name'];
                        }
                    }
                }
                $flat_item[] = array('item_id' => $item_id,'description' => $description, 'collection_id' => $collection_id, 'display_title' => $display_title, 
                                'shortcode' => '[popuparchive audio_file_id='.$audio_file_ids[0].' item_id='.$item_id.' collection_id='.$collection_id.']', 'embed_title' => $embed_title, 
                                'audio_items_count' => $audio_items_count, 'audio_file_ids' => $audio_file_ids, 'embed_code' => $embed_code,
                                'tags' => $tags, 'tag_count' => count($tags));
            } // end of conditional
        }
        return $flat_item;
    } else {
        return;
    }
}

/**
 * helper method that retreives the collection name 
 * 
 * @param integer $collection_id    unique identifier for the collection
 * @param array   $collections_data metadata associated with the collections
 * 
 * @todo have this pull from the stored data to save an API call
 * 
 * @return string
 */
function get_collection_name($collection_id, $collections_data)
{
   foreach ($collections_data['collections'] as $collection) {
        if ($collection['id'] == $collection_id) {
           return $collection['title'];
        }
   }
   return null;
}

/**
 * Method to get collections
 * 
 * This method retreives collections using the Pop Up Archive API
 * 
 * @param boolean $refresh variable to determine if refreshing of data is needed
 *
 * @todo update this method to allow for refresh by flusing the database
 *
 * @return array
 */
function get_collections($refresh = false)
{
    $stored_collections_data = get_option('popuparchive_collections_data');
    /* check to see if the collections are in the database */
    if ($stored_collections_data) {
        $collections_data = unserialize($stored_collections_data);
    } else {
        /* get the list of applicable collections from the API */
        $collections_data = pua_get_collections();
        store_data($collections_data,'popuparchive_collections_data');
    }
    return $collections_data;
}

/**
 * Get audio data and process it to remove extra information
 * 
 * @param array   $collections_data array of collection data
 * @param integer $collection_id    unique collection identifier
 * 
 * @return array
 * 
 */
function get_flat_audio_data($collections_data, $collection_id = null) {
    /* check for collection_id */
    if ($collection_id == null) {
        /* pick the first collection in the list for display */
        /* @todo update this to pick out the first collection that has items and strip out others */
        /* @todo update this to strip out private collections */
        $collection_id = $collections_data['collections'][0]['id'];
        $collection_name = $collections_data['collections'][0]['title'];
    }
    /* get the applicable audio items and strip out unneeded metadata */
    $flat_audio_data = pua_get_audio_items($collection_id);

    return $flat_audio_data;
}

/**
 * Store the audio data
 * 
 * This method stores the audio data in an appropriate database
 * 
 * @param array $collections_data array of collection data
 * 
 * @todo currently this method is a) not in use and b) writing to the options
 * database via the store_data() method. It should really be writing to 
 * either a custom post type -or- to a full-blown WP table. This should be
 * determined by the future business requirements for the plugin and performance. 
 * 
 * @return array 
 */
function store_flat_audio_data($collections_data) {
    /* pick the first collection in the list for display */
    /* @todo update this to pick out the first collection that has items and strip out others */
    /* @todo update this to strip out private collections */
    $collection_id = $collections_data['collections'][0]['id'];
    $collection_name = $collections_data['collections'][0]['title'];
    /* get the applicable audio items and strip out unneeded metadata */
    foreach ($collections_data['collections'] as $collection) {
        $flat_audio_data = pua_get_audio_items($collection['id']);
        store_data($flat_audio_data, 'popuparchive-collection-'.$collection['id']);
    }
    return $flat_audio_data;
}

/**
 * Stores data in the options table
 * 
 * @param array  $data      array of data to be stored
 * @param string $data_name name to store the data under
 */
function store_data($data, $data_name) {
    /* @todo store the PUA data in its own table */
    update_option($data_name, serialize($data));
}

/**
 * Helper function to strip out the extra JSON data and store 
 * just what is needed
 *
 * @param array $raw_collections list of collections returned from the API
 * @param int   $howMany         number of items to slice off from the array for display
 * @param int   $offset          how far to go into the array
 *
 * @return array
 */
function minimize_collection_data($raw_collections, $how_many = 0, $offset = 0)
{
    if ($how_many > 0) {
        $limited_collections = array_slice($raw_collections, $offset, $how_many);
    }

    return $limited_collections;
}

function display_collections_page_top($collections_data, $collection_name = '', $rendering_modal = false, $post_id = '') {
    $drop_down_option = array();
?>
    <h2>Your Pop Up Archive Audio Items</h2>
<?php
    if ($rendering_modal) {
?>
    <form action="media-upload.php?chromeless=1&amp;post_id=<?php echo $post_id; ?>&amp;tab=popuparchive_wp" method="post">
<?php
    } else {
?>
    <form action="admin.php?page=puawp_options&tab=puawp_display_page" method="post">
<?php
    }
?>
    <label>Choose a collection: </label>
        <select name="puawp-collections-filter">
<?php
       foreach($collections_data['collections'] as $collection) {
            $drop_down_option = '<option value="'.$collection['id'].'"';
            if (strtolower($collection_name) == strtolower($collection['title'])) {
                $drop_down_option .= ' selected=SELECTED">'.$collection['title'].'</option>';
            } else {
                $drop_down_option .= '>'.$collection['title'].'</option>';
            }
            echo $drop_down_option;
        }
?>
        </select>
        <span>&nbsp;<input name="puawp_refresh_table" type="submit" value="submit" class="button-primary" /></span>
    </form>

<?php
} // end of display_collections_page_top
