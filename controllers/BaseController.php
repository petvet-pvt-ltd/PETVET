<?php
abstract class BaseController {
    
    public function __construct() {
        // Base constructor - can be overridden by child classes
    }
    
    protected function view(string $module, string $name, array $data = []) {
        extract($data);
    $page = $name;
    $GLOBALS['module'] = $module;
    $GLOBALS['currentPage'] = $name . '.php';
    include __DIR__ . '/../views/shared/sidebar/sidebar.php';
    include __DIR__ . '/../views/'.$module.'/'.$name.'.php';
        //include __DIR__ . '/../views/shared/footer.php';
    }
    
    protected function viewStandalone(string $module, string $name, array $data = []) {
        extract($data);
        $page = $name;
        $GLOBALS['module'] = $module;
        $GLOBALS['currentPage'] = $name . '.php';
        // No sidebar - just the page
        include __DIR__ . '/../views/'.$module.'/'.$name.'.php';
    }
    
    protected function redirect(string $url) { header('Location: '.$url); exit; }
}
?>