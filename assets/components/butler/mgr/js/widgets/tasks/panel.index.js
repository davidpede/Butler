/**
 * @class Butler.panel.Tasks
 * @extends MODx.FormPanel
 * @param {Object} config An object of options.
 * @xtype butler-panel-tasks
 */
Butler.panel.Tasks = function(config) {
  config = config || {};
  Ext.applyIf(config,{
    border: false
    ,baseCls: 'modx-formpanel'
    ,cls: 'container'
    ,items: [{
      html: '<h2>Butler</h2>' // panel heading
      ,border: false
      ,cls: 'modx-page-header'
    },{
      layout: 'form'
      ,itemId: 'form'
      ,items: [{
        html: '<p>Page Description [panel.index.js]</p>'
        ,bodyCssClass: 'panel-desc'
        ,itemId: 'description'
        ,border: false
      }/*,{
        xtype: 'butler-grid-tasks' //xtype to display in this component
        ,itemId: 'grid'
        ,cls:'main-wrapper'
        ,preventRender: true
      }*/]
    }]
  });
  Butler.panel.Tasks.superclass.constructor.call(this,config);
};
Ext.extend(Butler.panel.Tasks,MODx.FormPanel); //register the type of component
Ext.reg('butler-panel-tasks',Butler.panel.Tasks); //register this component to its xtype