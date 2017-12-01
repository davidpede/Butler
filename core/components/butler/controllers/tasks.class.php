<?php
include_once dirname(dirname(__FILE__)) . '\model\butler\butler.class.php';
class ButlerTasksManagerController extends modExtraManagerController { //must match action var in menu
  public $butler;
  public function initialize() {
    $this->butler = new Butler($this->modx);
    $this->addJavascript($this->butler->config['jsUrl'].'butler.class.js');
    $this->addHtml(
      '<script type="text/javascript">
        Ext.onReady(function() {
          Butler.config = '.$this->modx->toJSON($this->butler->config).';
        });
      </script>'
    );
    return parent::initialize();
  }
  public function checkPermissions() { return true;}
  public function process(array $scriptProperties = array()) {}
  /**
     * Register custom CSS/JS for the page
     * @return void
     */
  public function loadCustomCssJs() { // load wigets from inside > out order
    //$this->addJavascript($this->butler->config['jsUrl'].'widgets/project/grid.js');
    $this->addJavascript($this->butler->config['jsUrl'].'widgets/tasks/panel.index.js');
    //$this->addJavascript($this->butler->config['jsUrl'].'widgets/project/window.all.js');
    $this->addLastJavascript($this->butler->config['jsUrl'].'widgets/tasks/section.index.js');
  }
  public function getPageTitle() {
    return 'Butler';
  }
  public function getTemplateFile() { return ''; }
}