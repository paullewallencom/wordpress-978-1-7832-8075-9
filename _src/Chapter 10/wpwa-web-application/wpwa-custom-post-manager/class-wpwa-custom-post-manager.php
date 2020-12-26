<?php



/*
 * Managing the initialization of custom post specific functions
*/
class WPWA_Custom_Post_Manager {

    private $base_path;
    private $template_parser;
    private $services;
    private $projects;
    private $books;
    private $articles;

    public function __construct() {

//        $this->base_path = plugin_dir_path(__FILE__);
//
//        require_once $this->base_path . 'twig_initializer.php';
//        $this->template_paser = Templates_Initializer::initialize_templates();
//
//
//        $this->services = new WPWA_Model_Service($this->template_paser);
//        $this->projects = new WPWA_Model_Project($this->template_paser);
//        $this->books = new WPWA_Model_Book($this->template_paser);
//        $this->articles = new WPWA_Model_Article($this->template_paser);

    }

    public function initialize(){
//        $this->base_path = plugin_dir_path(__FILE__);
//
//        require_once $this->base_path . 'twig_initializer.php';
        $this->template_parser = Twig_Initializer::initialize_templates();


        $this->services = new WPWA_Model_Service($this->template_parser);
        $this->projects = new WPWA_Model_Project($this->template_parser);
        $this->books = new WPWA_Model_Book($this->template_parser);
        $this->articles = new WPWA_Model_Article($this->template_parser);
    }

}

//$custom_post_manager = new WPWA_Custom_Post_Manager();
?>
