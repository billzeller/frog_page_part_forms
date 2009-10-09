<?php
AutoLoader::addFolder(dirname(__FILE__) . '/models');
AutoLoader::addFolder(dirname(__FILE__) . '/lib');

/**
 * Simple backend controller for custom page parts. Enables the FROG CMS to have custom page part forms.
 *
 * @version 0.0.7
 */
class PagePartFormsController extends PluginController {
    /* Plugin details */
    const PLUGIN_ID      = "page_part_forms";
    const PLUGIN_VERSION = "0.0.7";
    const PLUGIN_URL     = "plugin/page_part_forms/";

    /* Location of the view folder */
    const VIEW_FOLDER            = "page_part_forms/views/";
    const PLUGIN_REL_VIEW_FOLDER = "../../plugins/page_part_forms/views/";

    /* For css */
    const CSS_ID_PREFIX    = "Page-part-forms-";
    const CSS_CLASS_PREFIX = "page-part-forms-";

    /* Structure properties */
    const PROPERTY_NAME   = "name";
    const PROPERTY_LIMIT  = "limit";
    const PROPERTY_VALUES = "values";
    const PROPERTY_TYPE   = "type";
    const PROPERTY_TITLE  = "title";

    /* Structure types */
    const TYPE_PAGE_PART  = "page_part";
    const TYPE_TEXT       = "text";
    const TYPE_SELECT     = "select";
    const TYPE_DATE       = "date";
    
    /* Threshold for select type
       .... 5 .... 10 ....
       few    more    many
    */
    const SELECT_MORE_THRESHOLD = 5;
    const SELECT_MANY_THRESHOLD = 10;

    /* Array access */
    const SELECT_INDEX_SELECT_ONE  = 0;
    const SELECT_INDEX_SELECT_MANY = 1;
    
    const SELECT_INDEX_AMOUNT_FEW  = 0;
    const SELECT_INDEX_AMOUNT_MORE = 1;
    const SELECT_INDEX_AMOUNT_MANY = 2;

    /* Select types */
    const SELECT_TYPE_DROPDOWN  = "dropdown";
    const SELECT_TYPE_LIST_ONE  = "select_list_one";
    const SELECT_TYPE_LIST_MORE = "select_list_more";
    const SELECT_TYPE_RADIO     = "radio";
    const SELECT_TYPE_CHECKBOX  = "checkbox";
    
    /* Decision table for the select type */
    private static $Select_type = array(
      // SELECT_INDEX_SELECT_ONE
        // display type based on thresholds
        array(self::SELECT_TYPE_RADIO,    self::SELECT_TYPE_LIST_ONE, self::SELECT_TYPE_DROPDOWN),
      // SELECT_INDEX_SELECT_MANY
        // type based on threshold
        array(self::SELECT_TYPE_CHECKBOX, self::SELECT_TYPE_LIST_MORE, self::SELECT_TYPE_DROPDOWN),
    );

    /* Singleton instance for observer */
    private static $Instance;

    /**
     * Registers the plugin and controller in the system as well as the observers.
     */
    public static function Init() {
      // Register plugin
      Plugin::setInfos(array(
          'id'          => self::PLUGIN_ID,
          'title'       => __('Page Part Forms'),
          'description' => __('Allows to create custom page part forms'),
          'version'     => self::PLUGIN_VERSION,
         	'license'     => 'AGPL',
        	'author'      => 'THE M',
          'website'     => 'http://github.com/them/frog_page_part_forms/',
          'update_url'  => 'http://github.com/them/frog_page_part_forms/raw/master/frog-plugins.xml',
          'require_frog_version' => '0.9.5'
      ));

      // Register controller
      Plugin::addController(self::PLUGIN_ID, __('Page Part Forms'), 'administrator, developer', true);
      
      // Add extra scripting for JSON
      Plugin::addJavascript(self::PLUGIN_ID, "labs_json.js");
    
      // The callbacks for the backend
      Observer::observe('view_page_page_metadata', __CLASS__.'::callback_view_page_page_metadata');
      Observer::observe('view_page_edit_popup', __CLASS__.'::callback_view_page');
    }

    /**
     * Returns the instance of the controller.
     *
     * @return the instance of the controller object
     */
    public static function Get_instance() {
      if (!self::$Instance) {
        $class = __CLASS__;
        self::$Instance = new $class(false);
      }

      return self::$Instance;
    }

    /**
     * Create a new controller instance and apply the sidebar to the backend.
     */
    public function __construct($check_permissions = true) {
      AuthUser::load();
      if (!AuthUser::isLoggedIn()) {
        redirect(get_url('login'));
      }

      if ($check_permissions) {
        // This is developer plugin
        if (!AuthUser::hasPermission('administrator') && !AuthUser::hasPermission('developer')) {
          redirect(get_url());
        }
      }

      $this->setLayout('backend');
      $this->assignToLayout('sidebar', $this->create_view('sidebar'));
    }

    /**
     * Catch non-existing controller requests to home.
     */
    public function __call($name, $args) {
      redirect(get_url(''));  
    }

    /**
     * Private function that provide the default values for the view.
     *
     * @return default values
     */
    private function get_default_view_vars() {
      $vars = array();
      
      $vars['plugin_id'] = self::PLUGIN_ID;
      $vars['css_id_prefix'] = self::CSS_ID_PREFIX;
      $vars['css_class_prefix'] = self::CSS_CLASS_PREFIX;
      $vars['plugin_url'] = get_url('plugin/'.self::PLUGIN_ID.'/');
      
      return $vars;
    }
    
    /**
     * Overwrite the render function to enforce that some variables are
     * available for the whole view artifacts.
     * Simplify the view file handling by prefixing the file with the
     * plugin directory.
     *
     * @param view the view file
     * @param vars parameter for the views
     * @return the view
     */
    /*@overwrite('render')*/
    public function render($view, $vars=array()) {
      $vars = array_merge($this->get_default_view_vars(), $vars);

      /* We only render views for this plugin. So add the prefix of the view folder to every view file. */      
      return parent::render(self::VIEW_FOLDER.$view, $vars);
    }

    /**
     * View factory for the controller and the view.
     *
     * @param view the filename without the postfix
     * @param vars the template vars
     * @return a view object
     */
    private function create_view($view, $vars=array()) {
      $vars = array_merge($this->get_default_view_vars(), $vars);
        
      return new View(self::PLUGIN_REL_VIEW_FOLDER.$view, $vars);
    }

    /**
     * Show all page part forms.
     */
    public function index() {
      $this->display('index', array(
        'page_part_forms' => Record::findAllFrom('PagePartForm', '1=1 ORDER BY name DESC'),
      ));
    }
    
    /**
     * Simple function that checks if the posted data is valid.
     * As side effet, the 'flash' values are set.
     *
     * @param data the post data from the user
     * @return data is valid
     */
    private function check_constraints($data) {
      if (!isset($data["name"]) || empty($data["name"])) {
        Flash::set('error', __('You have to specify a name!'));
        Flash::init(); // XXX: force flash values availability
        return false;
      }
      
      return true;
    }
    
    /**
     * Returns the posted data for the page part form from the user.
     *
     * @return the data for this page part form
     */
    private static function Get_data() {
      return isset($_POST[self::PLUGIN_ID]) ? $_POST[self::PLUGIN_ID] : array();
    }
    
    /**
     * Adds a new page part form.
     *
     */
    public function add() {
      $data = self::Get_data();

      // NOTE: null pattern
      $null_object = new PagePartForm($data);      

      if (get_request_method() == 'POST' && $this->check_constraints($data)) {
        $this->update($null_object, $data);
      }

      $this->display('edit', array(
        'action'         => 'add',
        'page_part_form' => $null_object
      ));
    }
    
    /**
     * Edit a page part form.
     *
     * @param id the id of the page part form to be edited
     */
    public function edit($id) {
      if (!$page_part_form = Record::findByIdFrom('PagePartForm', $id)) {
        Flash::set('error', __('Page part form not found'));
        redirect(get_url(self::PLUGIN_URL));
      }
    
      $data = self::Get_data();
      if (get_request_method() == 'POST' && $this->check_constraints($data)) {
        $this->update($page_part_form, $data);
      }

      $this->display('edit', array(
        'action'            => 'edit',
        'page_part_form'    => $page_part_form,
        'outline_structure' => $this->create_view('structure', array(
          'structure' => self::Get_structure($page_part_form)
        ))
      ));
    }

    /**
     * Deletes a given page part form.
     *
     * @param id the id of the page part form to be deleted
     */
    public function delete($id) {
      // Find the page part form with given id
      if ($page_part_form = Record::findByIdFrom('PagePartForm', $id)) {
        if ($page_part_form->delete()) {
          Flash::set('success', __('Page part form :name has been deleted!', array(':name' => $page_part_form->name)));
        }
        else {
          Flash::set('error', __('Page part form :name has not been deleted!', array(':name' => $page_part_form->name)));
        }
      }
      else {
        Flash::set('error', __('Page part form not found!'));
      }

      redirect(get_url(self::PLUGIN_URL));
    }
 
    /**
     * Update an existing page part form with the new values from the backend form
     *
     * @param page_part_form the entity from the database
     * @param data the new data to apply
     */
    private function update($page_part_form, $data) {
      // Apply data
      $page_part_form->setFromData($data);  
        
      if (!$page_part_form->save()) {
        Flash::set('error', __("Unknown error saving page part form"));
        redirect(get_url(self::PLUGIN_URL.'edit/'.$id));
      }
      else {
        Flash::set('success', __("Page part form saved"));
      }

      if (isset($_POST['commit'])) {
        redirect(get_url(self::PLUGIN_URL));
      }
      else {
        redirect(get_url(self::PLUGIN_URL.'edit/'.$page_part_form->id));
      }
    }
    
    public function settings() {
      // No sidebar
      $this->assignToLayout('sidebar', null);
      $this->display('settings');
    }
    
    public function cleanup() {
      // XXX: because there is no permission check from the backend, it must be done here
      if (!AuthUser::hasPermission('administrator') && !AuthUser::hasPermission('developer')) {
        redirect(get_url());
      }

      $table_name = TABLE_PREFIX.PagePartForm::TABLE_NAME;

      // Connection
      $pdo = Record::getConnection();

      // Clean metadata
      $pdo->exec("DROP TABLE $table_name");

      Flash::set('success', __("Table for Page Part Forms deleted. Disable the plugin now."));
      redirect(get_url('setting'));
    }
    
    /**
     * Applies the defined structure to a valid structure accepted by this plugin.
     *
     * @param the structure edited by the user
     * @return a valid structure with all fields and default values if necessary
     */
    private static function Get_structure($page_part_form) {
      $user_structure = Spyc::YAMLLoadString($page_part_form->definition);

      $structure = array();      
      foreach ($user_structure as $structure_element_name => $structure_element) {
        $element = array(); 
          
        // If there is just junk, erase it!
        if (!is_array($structure_element)) {
          $structure_elmement = array();
        }

        // Apply general values or defaults
        $element[self::PROPERTY_NAME] = $structure_element_name;
        self::Array_get_set($structure_element, $element, self::PROPERTY_TITLE, $structure_element_name);
        self::Array_get_set($structure_element, $element, self::PROPERTY_TYPE, self::TYPE_PAGE_PART);

        // Special handling for type. Accept only valid entries
        switch($element[self::PROPERTY_TYPE]) {
          case self::TYPE_SELECT:
            // Get values
            if (isset($structure_element[self::PROPERTY_VALUES]) && is_array($structure_element[self::PROPERTY_VALUES])) {
              $element[self::PROPERTY_VALUES] = array();
              
              foreach ($structure_element[self::PROPERTY_VALUES] as $value) {
                if (!is_array($value)) {
                  $element[self::PROPERTY_VALUES][] = $value;
                }
              }

              // Limit selection
              self::Array_get_set($structure_element, $element, self::PROPERTY_LIMIT, 1);
            }
            else {
              // Ignore this element
              continue 2; // XXX: PHP is strange!
            }
            break;
            
          case self::TYPE_TEXT:
            // Limit selection
            self::Array_get_set($structure_element, $element, self::PROPERTY_LIMIT, 255);
            break;
            
          case self::TYPE_DATE:
            // Noting to apply
            break;
          default:
            $element[self::PROPERTY_TYPE] = self::TYPE_PAGE_PART;
            break;
        }
       
        // Add element to structure
        $structure[$structure_element_name] = $element;
      }

      return $structure;
    }

    /**
    * Small helper funciton to set values in an array
    *
    * @param source the source array
    * @param target the target array
    * @param keyword the keyword of the value for the source and target array
    * @param default if no value was found use this default value
    * @return void
    */
    private static function Array_get_set(&$source, &$target, $keyword, $default) {
        if (isset($source[$keyword])) {
          $target[$keyword] = $source[$keyword];
        }
        else {
          if (isset($default)) {
            $target[$keyword] = $default;
          }
        }
    }

  /**
   * Select page part forms for metadata.
   *
   * @param metadata metadata entries for this page
   */
  public static function callback_view_page_page_metadata($metadata) {
    $selected = "";
    $children_selected = "";

    // Search for metadat for this plugin
    foreach ($metadata as $m) {
      if ($m->keyword == self::PLUGIN_ID) {
        $selected = $m->value;
      }
      if ($m->keyword == self::PLUGIN_ID.'_children') {
	$children_selected = $m->value;
      }
    }

    self::Get_instance()->create_view('observers/form_type', array(
       'page_part_forms' => Record::findAllFrom('PagePartForm', '1=1 ORDER BY name DESC'),
       'selected'        => $selected,
       'children_selected' => $children_selected,
    ))->display();
  }

  /**
   * View callback function. Adds the page_part_form to the admin page.
   *
   * @param page the current page to edit
   */
  public static function callback_view_page($page) {
    // Because the metadata is not visible, we can't use $page->metadata[self::PLUGIN_ID]
    if ((isset($page->id) && $form = PageMetadata::FindOneByPageAndKeyword($page->id, self::PLUGIN_ID))
    	 || ($form = PageMetadata::FindOneByPageAndKeyword($page->parent_id, self::PLUGIN_ID.'_children'))) {
      if ($definition = Record::findByIdFrom('PagePartForm', $form->value)) {

        // Convert page_parts array to hash
        $page_parts = array();
	if(isset($page->id)){
      	  foreach (PagePart::findByPageId($page->id) as $page_part) {
        	  $page_parts[$page_part->name] = $page_part;
          }
	}
        
        // Add the page_part_form to the admin view
        self::Get_instance()->create_view('observers/page_form', array(
          'page'       => $page,
          'page_parts' => $page_parts,
          'structure'  => self::Get_structure($definition),
        ))->display();
      }
    }
  }
  
  /**
   * Because Frog can only handle simple string content, multiple values are encoded.
   *
   * @param $content the content from the page_part
   * @return a hash with the content values as keys
   */
  public static function Get_multiple_values($content) {
    $array = json_decode($content);
    
    // Use the raw content as array
    if (!is_array($array)) {
      if ($array) {
        $array = array($array);
      }
      else {
        $array = array();
      }
    }

    // Array to hash    
    return array_flip($array);
  }
  
  /**
   * Returns the recommended interface type of the view based on the number of choices and elements.
   *
   * @param definition the definition of this form type
   * @return the interface type for this definition
   */
  public static function Get_select_type($definition) {
    $limit_index = $definition[PagePartFormsController::PROPERTY_LIMIT] == 1 ? self::SELECT_INDEX_SELECT_ONE : self::SELECT_INDEX_SELECT_MANY;

    $amount = count($definition[PagePartFormsController::PROPERTY_VALUES]);      
    $amount_index;

    if ($amount >= self::SELECT_MANY_THRESHOLD) {
      $amount_index = self::SELECT_INDEX_AMOUNT_MANY;                
    }
    elseif ($amount >= self::SELECT_MORE_THRESHOLD) {
      $amount_index = self::SELECT_INDEX_AMOUNT_MORE;
    }
    else {
      $amount_index = self::SELECT_INDEX_AMOUNT_FEW;
    }
    
    return self::$Select_type[$limit_index][$amount_index];
  }
}
?>
